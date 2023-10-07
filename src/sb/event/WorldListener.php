<?php

declare(strict_types = 1);

namespace sb\event;

use pocketmine\entity\Location;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTrampleFarmlandEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use sb\world\sound\SoundPlayer;
use sb\player\CorePlayer;
use sb\world\area\AreaFactory;
use sb\world\WorldManager;

use pocketmine\event\Listener;
use pocketmine\event\player\{
	PlayerExhaustEvent,
	PlayerMoveEvent,
	PlayerToggleFlightEvent};

use pocketmine\event\entity\EntityTeleportEvent;

//todo: area stuff
class WorldListener implements Listener {
	public static array $spawn = [];

	public array $cache = [], $nether = [];

	public function onEntityLevelChange(EntityTeleportEvent $event) : void {
		$entity = $event->getEntity();

		if($entity instanceof CorePlayer) {
			foreach(WorldManager::getInstance()->getHolograms() as $hologram) {
				if($hologram->getPos()->getWorld()->getFolderName() !== $event->getTo()->getWorld()->getFolderName()) {
					$hologram->despawnFrom($entity);
					return;
				}
				$hologram->updateFor($entity);
			}
		}
	}

	public function onMove(PlayerMoveEvent $event)
	{
		$player = $event->getPlayer();
		$areas = AreaFactory::getInstance()->getInPosition($player->getPosition()->asPosition());

		if ($event->isCancelled()) return;

		$fly = false;

		if ($areas !== null) {
			foreach ($areas as $area) {
				if($area->getName() === "spawn" && !isset(self::$spawn[$player->getName()])) {
					//todo: wtf. change when areas are made
					if(isset($this->cache[$player->getName()])) {
						unset($this->cache[$player->getName()]);
					}
					self::$spawn[$player->getName()] = $player;
					$player->sendMessage(TextFormat::colorize("&r&a ~ Safezone - PvP is disabled here."));
					$player->sendTip(TextFormat::colorize("&r&a(!) Safezone &2- &aPvP Disabled"));
					return;
				}
				if($area->getName() === "spawn" && !isset($this->cache[$player->getName()])) {
					if (isset(self::$spawn[$player->getName()])) {
						unset(self::$spawn[$player->getName()]);
					}
					$this->cache[$player->getName()] = $player;
					$player->sendMessage(TextFormat::colorize("&r&4 ~ Warzone - PvP is enabled here."));
					$player->sendTip(TextFormat::colorize("&r&4(!) Warzone &c- &4PvP Enabled"));
					return;
				}
			}
		}
		if ($fly) {
			$player->setAllowFlight(true);
		} else {
			/**if ($player->getGamemode()->id() !== GameMode::CREATIVE()->id() && !$player->flying) {
				if(!StaffManager::isInStaffMode($player)) {
					$player->setAllowFlight(false);
					$player->setFlying(false);
				}
			}	TODO
			 */
		}
		if ($areas === null) {
			//shouldn't happen.
			return;
		}
		$def = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation();
		$spawn2 = new Location($def->getFloorX(), $def->getFloorY(), $def->getFloorZ(), Server::getInstance()->getWorldManager()->getDefaultWorld(), 0.0, 0.0);

		if ($spawn2->distance($player->getLocation()) >= 10000) {
			$event->cancel();
		}
	}

	public function onToggleFly(PlayerToggleFlightEvent $event) {
		/**
		 * @param CorePlayer
		 */
		$player = $event->getPlayer();

		if (!$player->isCombatTagged() && $player->getAllowFlight() === false) {
			$player->setAllowFlight(true);
		}
		if (!$event->isFlying()) {
			$player->setFlying(true);
			SoundPlayer::play($player, "firework.blast");
		}
		if ($event->isFlying()) {
			$player->setFlying(false);
			SoundPlayer::play($player, "firework.launch");
		}
	}

	public function onBreak(BlockBreakEvent $event): void {
		$player = $event->getPlayer();
		$areas = AreaFactory::getInstance()->getInPosition($player->getPosition()->asPosition());

		if($areas !=null) {
			foreach($areas as $area) {
				if(!$area->getEditFlag() and !$player->hasPermission("areas.bypass")) {
					$event->cancel();
					return;
				}
			}
		}
	}

	public function onTrample(EntityTrampleFarmlandEvent $ev): void {
		$block = $ev->getBlock();
		$areas = AreaFactory::getInstance()->getInPosition($block->getPosition()->asPosition());

		if ($areas !== null) $ev->cancel();
	}

	public function onUpdate(BlockUpdateEvent $ev): void {
		$block = $ev->getBlock();
		$areas = AreaFactory::getInstance()->getInPosition($block->getPosition()->asPosition());

		if ($areas !== null) $ev->cancel();
	}

	public function onPlace(BlockPlaceEvent $event): void {
		$player = $event->getPlayer();

		if (!$player instanceof CorePlayer) {
			return;
		}
		$areas = AreaFactory::getInstance()->getInPosition($player->getPosition()->asPosition());

		if($areas !=null) {
			foreach($areas as $area) {
				if(!$area->getEditFlag() and !$player->hasPermission("areas.bypass")) {
					$event->cancel();
					return;
				}
			}
		}
	}

	public function onPlayerExhaust(PlayerExhaustEvent $event): void {
		/**@var CorePlayer $player */
		$player = $event->getPlayer();

		$areas = AreaFactory::getInstance()->getInPosition($player->getPosition()->asPosition());

		if($areas != null) {
			foreach($areas as $area) {
				if(!$area->getPvpFlag() and !$player->hasPermission("areas.bypass")) {
					$event->cancel();
					return;
				}
			}
		}
	}


	/**
	 * @priority LOWEST
	 * @param EntityDamageEvent $event
	 */
	public function onEntityDamage(EntityDamageEvent $event): void {
		$entity = $event->getEntity();
		if(!$entity instanceof CorePlayer) {
			return;
		}
		$areas = AreaFactory::getInstance()->getInPosition($entity->getPosition()->asPosition());

		if($areas !== null) {
			foreach($areas as $area) {
				if($area->getPvpFlag() === false) {
					$event->cancel();
					return;
				}
			}
		}
	}

	/**
	 * @priority LOWEST
	 * @param ProjectileLaunchEvent $event
	 */
	public function onProjectileLaunch(ProjectileLaunchEvent $event): void {
		$entity = $event->getEntity();
		if(!$entity instanceof CorePlayer) {
			return;
		}
		$areas = AreaFactory::getInstance()->getInPosition($entity->getPosition()->asPosition());

		if($areas !== null) {
			foreach($areas as $area) {
				if($area->getPvpFlag() === false) {
					$event->cancel();
					return;
				}
			}
		}
	}
}