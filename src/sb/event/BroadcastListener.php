<?php

declare(strict_types = 1);

namespace sb\event;

use sb\server\broadcast\BroadcastHandler;
use sb\server\ServerManager;
use sb\Skyblock;

use sb\server\broadcast\Broadcasts;
use sb\player\{
	CorePlayer,
	CoreUser,
	traits\PlayerCallTrait};

use pocketmine\event\Listener;

use pocketmine\event\player\{
	PlayerDeathEvent,
	PlayerJoinEvent,
	PlayerPreLoginEvent,
	PlayerQuitEvent
};
use pocketmine\event\entity\{
	EntityDamageEvent,
	EntityDamageByBlockEvent,
};

use pocketmine\event\server\DataPacketReceiveEvent;

use pocketmine\network\mcpe\protocol\{
	LoginPacket,
	ProtocolInfo
};

use pocketmine\entity\Living;

use pocketmine\Server;

class BroadcastListener implements Listener {
	use PlayerCallTrait;

	public function onPlayerDeath(PlayerDeathEvent $event) : void {
		$player = $event->getEntity();

		if($player instanceof CorePlayer) {
			$player->getCoreUser()->setDeaths($player->getCoreUser()->getDeaths() + 1);
			$player->getCoreUser()->setKillStreak(0);
			$replaces = [
				"{PLAYER}" => $player->getName()
			];
			$message = "";
			$cause = $player->getLastDamageCause();

			switch($cause->getCause()) {
				case EntityDamageEvent::CAUSE_CONTACT:
					$stringCause = "contact";

					if($cause instanceof EntityDamageByBlockEvent) {
						$replaces["{BLOCK}"] = $cause->getDamager()->getName();
						break;
					}
					$replaces["{BLOCK}"] = "unknown";
					break;
				case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
					$stringCause = "kill";
					$killer = $cause->getEntity();

					if($killer instanceof Living) {
						if($killer instanceof CorePlayer) {
							$killer->getCoreUser()->setKillStreak($killer->getCoreUser()->getKillStreak() + 1);
							$killer->getCoreUser()->setKills($killer->getCoreUser()->getKills() + 1);
						}
						$replaces["{KILLER}"] = $killer->getName();
						break;
					}
					$replaces["{KILLER}"] = "unknown";
					break;
				case EntityDamageEvent::CAUSE_PROJECTILE:
					$stringCause = "projectile";
					$killer = $cause->getEntity();

					if($killer instanceof Living) {
						if($killer instanceof CorePlayer) {
							$killer->getCoreUser()->setKillStreak($killer->getCoreUser()->getKillStreak() + 1);
							$killer->getCoreUser()->setKills($killer->getCoreUser()->getKills() + 1);
						}
						$replaces["{KILLER}"] = $killer->getName();
						break;
					}
					$replaces["{KILLER}"] = "unknown";
					break;
				case EntityDamageEvent::CAUSE_SUFFOCATION:
					$stringCause = "suffocation";
					break;
				case EntityDamageEvent::CAUSE_STARVATION:
					$stringCause = "starvation";
					break;
				case EntityDamageEvent::CAUSE_FALL:
					$stringCause = "fall";
					break;
				case EntityDamageEvent::CAUSE_FIRE:
					$stringCause = "fire";
					break;
				case EntityDamageEvent::CAUSE_FIRE_TICK:
					$stringCause = "on-fire";
					break;
				case EntityDamageEvent::CAUSE_LAVA:
					$stringCause = "lava";
					break;
				case EntityDamageEvent::CAUSE_DROWNING:
					$stringCause = "drowning";
					break;
				case EntityDamageEvent::CAUSE_ENTITY_EXPLOSION:
				case EntityDamageEvent::CAUSE_BLOCK_EXPLOSION:
					$stringCause = "explosion";
					break;
				case EntityDamageEvent::CAUSE_VOID:
					$stringCause = "void";
					break;
				case EntityDamageEvent::CAUSE_SUICIDE:
					$stringCause = "suicide";
					break;
				case EntityDamageEvent::CAUSE_MAGIC:
					$stringCause = "magic";
					break;
				default:
					$stringCause = "normal";
					break;
			}
			if(!empty(Broadcasts::DEATHS[$stringCause])) {
				$message = Broadcasts::DEATHS[$stringCause];

				foreach($replaces as $key => $value) {
					$message = str_replace([
						"{" . $key . "}",
						"{PLAYER}"
					], [
						$value,
						$player->getName()
					], $message);
				}
			}
			$event->setDeathMessage($message);
		}
	}

	public function onPlayerJoin(PlayerJoinEvent $event) : void {
		$player = $event->getPlayer();

		if($player instanceof CorePlayer) {
			$message = "";

			if(!$player->hasPlayedBefore()) {
				if(!empty(Broadcasts::JOINS["first"])) {
					$rank = $player->getCoreUser()->getRank()->getNameTagFormatFor($player);

					$message = str_replace([
						"{PLAYER}",
						"{TIME}",
						"{NAME_TAG_FORMAT}"
					], [
						$player->getName(),
						date(Broadcasts::FORMATS["date_time"]),
						str_replace("{DISPLAY_NAME}", $player->getName(), $rank)
					], Broadcasts::JOINS["first"]);
				}
			} else {
				$rank = $player->getCoreUser()->getRank()->getNameTagFormatFor($player);
				if(!empty(Broadcasts::JOINS["normal"])) {
					$message = str_replace([
						"{PLAYER}",
						"{TIME}",
						"{NAME_TAG_FORMAT}"
					], [
						$player->getName(),
						date(Broadcasts::FORMATS["date_time"]),
						str_replace("{DISPLAY_NAME}", $player->getName(), $rank)
					], Broadcasts::JOINS["normal"]);
				}
			}
			$event->setJoinMessage($message);
		}
	}

	public function onPlayerPreLogin(PlayerPreLoginEvent $event) : void {
		$playerInfo = $event->getPlayerInfo();

		$this->getCoreUser($playerInfo->getUsername(), function(?CoreUser $user) use($playerInfo, $event) {
			$message = "";

			if(count(Server::getInstance()->getOnlinePlayers()) - 1 < Server::getInstance()->getMaxPlayers()) {
				if(!empty(Broadcasts::KICKS["whitelisted"])) {
					$message = str_replace([
						"{PLAYER}",
						"{TIME}",
						"{ONLINE_PLAYERS}",
						"{MAX_PLAYERS}",
						"{PREFIX}"
					], [
						$playerInfo->getUsername(),
						date(Broadcasts::FORMATS["date_time"]),
						count(Server::getInstance()->getOnlinePlayers()),
						Server::getInstance()->getMaxPlayers(),
						Skyblock::PREFIX
					], Broadcasts::KICKS["whitelisted"]);
				}
				if(!Server::getInstance()->isWhitelisted($playerInfo->getUsername())) {
					$event->setKickFlag($event::KICK_FLAG_SERVER_WHITELISTED, $message);
				}
			} else {
				if($user->loaded()) {
					if(!$user->hasPermission("server.full")) {
						if(!empty(Broadcasts::KICKS["full"])) {
							$message = str_replace([
								"{PLAYER}",
								"{TIME}",
								"{ONLINE_PLAYERS}",
								"{MAX_PLAYERS}",
								"{PREFIX}"
							], [
								$playerInfo->getUsername(),
								date(Broadcasts::FORMATS["date_time"]),
								count(Server::getInstance()->getOnlinePlayers()),
								Server::getInstance()->getMaxPlayers(),
								Skyblock::PREFIX
							], Broadcasts::KICKS["full"]);

							$event->setKickFlag($event::KICK_FLAG_SERVER_FULL, $message);
						}
					}
				}
			}
		});
	}

	public function onPlayerQuit(PlayerQuitEvent $event) : void {
		$player = $event->getPlayer();

		if($player instanceof CorePlayer) {
			$message = "";

			if($player->hasPermission("core.broadcast.quit")) {
				if(!empty(Broadcasts::QUITS["normal"])) {
					$message = str_replace([
						"{PLAYER}",
						"{TIME}",
						"{NAME_TAG_FORMAT}"
					], [
						$player->getName(),
						date(Broadcasts::FORMATS["date_time"]),
						str_replace("{DISPLAY_NAME}", $player->getName(), $player->getCoreUser()->getRank()->getNameTagFormatFor($player))
					], Broadcasts::QUITS["normal"]);
				}
			}
			$event->setQuitMessage($message);
			$player->leave();
		}
	}

	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void {
		$player = $event->getOrigin();
		$pk = $event->getPacket();

		if($player instanceof CorePlayer) {
			if($pk->pid() == LoginPacket::NETWORK_ID) {
				if($pk->protocol < ProtocolInfo::CURRENT_PROTOCOL) {
					if(!empty(Broadcasts::KICKS["outdated"]["client"])) {
						$message = str_replace(["{PLAYER}", "{TIME}"], [$player->getName(), date(Broadcasts::FORMATS["date_time"])], BroadcastHandler::KICKS["outdated"]["client"]);
						$player->close($message);
						$event->cancel();
					}
				} else if($pk->protocol > ProtocolInfo::CURRENT_PROTOCOL) {
					if(!empty(Broadcasts::KICKS["outdated"]["server"])) {
						$message = str_replace(["{PLAYER}", "{TIME}"], [$player->getName(), date(Broadcasts::FORMATS["date_time"])], BroadcastHandler::KICKS["outdated"]["server"]);

						$player->close($message);
						$event->cancel();
					}
				}
			}
		}
	}
}