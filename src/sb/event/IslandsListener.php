<?php 

namespace sb\event;

use pocketmine\block\tile\Container;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use sb\event\player\PlayerFishEvent;
use sb\islands\Island;
use sb\islands\IslandManager;
use sb\islands\traits\IslandCallTrait;
use sb\islands\utils\IslandStats;
use sb\permission\utils\IslandPermissions;
use sb\player\CorePlayer;
use sb\Skyblock;

class IslandsListener implements Listener{
	use IslandCallTrait;

	public function onDeath(PlayerDeathEvent $event){
		$player = $event->getPlayer();

		$cause = $player->getLastDamageCause();

		switch ($cause->getCause()) {
			case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
				$killer = $cause->getEntity();

				if ($killer instanceof CorePlayer) {
					if ($killer->getIslandName() !== null) {
						if (mt_rand(0, 4) > 3) {
							$this->getIsland($killer->getCoreUser()->getIsland(), function (Island $island) use ($killer, $player) {
								if (is_null($island)) return; //weird

								$island->addPower(10);
								$this->getIsland($player->getCoreUser()->getIsland(), function (Island $island2) use ($killer, $player) {
									if (is_null($island2)) return; //weird

									$island2->removePower(10);

									$island2->announce(Skyblock::PREFIX . "§c{$player->getName()}§7 died to §c{$killer->getName()}§7 and reduced your Island power by 10");
								});
								$island->announce(Skyblock::PREFIX . "§c{$killer->getName()}§7 killed §c{$player->getName()}§7 and increased your Island power by 10");
							});
						}
					}
				}
				break;
			case EntityDamageEvent::CAUSE_PROJECTILE:
				$killer = $cause->getEntity();

				if ($killer instanceof CorePlayer) {
					if ($killer->getIslandName() !== null) {
						if (mt_rand(0, 4) > 3) {
							$this->getIsland($killer->getCoreUser()->getIsland(), function (Island $island) use ($killer, $player) {
								if (is_null($island)) return; //weird

								$island->addPower(10);
								$this->getIsland($player->getCoreUser()->getIsland(), function (Island $island2) use ($killer, $player) {
									if (is_null($island2)) return; //weird

									$island2->removePower(10);

									$island2->announce(Skyblock::PREFIX . "§c{$player->getName()}§7 died to §c{$killer->getName()}§7 and reduced your Island power by 10");
								});
								$island->announce(Skyblock::PREFIX . "§c{$killer->getName()}§7 killed §c{$player->getName()}§7 and increased your Island power by 10");
							});
						}
					}
				}
				break;
		}
	}

	public function onFish(PlayerFishEvent $event)
	{
		$player = $event->getPlayer();

		if ($player instanceof CorePlayer && $player->getCoreUser()->hasIsland()) {
			$this->getIsland($player->getCoreUser()->getIsland(), function (?Island $island) use ($player) {
				if (is_null($island)) {
					return;
				}
				$island->addXp(IslandStats::XP[IslandStats::CROPS_HARVESTED]);
			});
		}
	}

	public function onBreak(BlockBreakEvent $event): void
	{
		$player = $event->getPlayer();
		$world = $player->getWorld();

		if (!is_null($island = IslandManager::getInstance()->getIslandFromName($player->getWorld()->getFolderName()))) {
			if (IslandManager::getInstance()->isIslandWorld($world) && $player->isCreative() === false) {
				if ($island->isMember($player->getCoreUser())) {
					if (!$island->getRole($player->getCoreUser())->hasPermission(IslandPermissions::PERMISSION_BREAK)) {
						$player->sendMessage(Skyblock::ERROR_PREFIX . "You have no permissions to §cbreak blocks§7.");
						$event->cancel();
					}
				} else $event->cancel();
			}
			$island->handleBlockBreak($event);
		}
	}

	public function onPlace(BlockPlaceEvent $event): void
	{
		$player = $event->getPlayer();
		$world = $player->getWorld();

		if (!is_null($island = IslandManager::getInstance()->getIslandFromName($player->getWorld()->getFolderName()))) {
			if (IslandManager::getInstance()->isIslandWorld($world) && $player->isCreative() === false) {
				if ($island->isMember($player->getCoreUser())) {
					if (!$island->getRole($player->getCoreUser())->hasPermission(IslandPermissions::PERMISSION_BUILD)) {
						$player->sendMessage(Skyblock::ERROR_PREFIX . "You have no permissions to §cplace blocks§7.");
						$event->cancel();
					}
				} else $event->cancel();
			}
			$island->handleBlockPlace($event);
		}
	}

	public function onInteract(PlayerInteractEvent $event): void{
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$world = $player->getWorld();
		$island = IslandManager::getInstance()->getIslandFromName($player->getWorld()->getFolderName());

		if (!is_null($island)) {
			if (IslandManager::getInstance()->isIslandWorld($world) && $player->isCreative() === false) {
				if ($island->isMember($player->getCoreUser())) {
					if ($world->getTile($block->getPosition()) instanceof Container && !$island->getRole($player->getCoreUser())->hasPermission(IslandPermissions::PERMISSION_OPEN)) {
						$player->sendMessage(Skyblock::ERROR_PREFIX . "You have no permissions to §copen containers§7.");
						$event->cancel();
					} else if (!$player->isCreative()) {
						$event->cancel();
					}
				} else {
					$event->cancel();
				}
			}
		}
	}
	/**
	 * Handle damage caused by entities.
	 *
	 * @param EntityDamageByEntityEvent $event
	 * @return void
	 */
	public function onEntityDamageByEntityEvent(EntityDamageByEntityEvent $event): void{
		$event->uncancel();
		$damager = $event->getDamager();
		$world = $event->getEntity()->getWorld();

		if ($damager instanceof CorePlayer) {
			$island = IslandManager::getInstance()->getIslandFromName($damager->getWorld()->getFolderName());

			if (!is_null($island) && IslandManager::getInstance()->isIslandWorld($world) && !$damager->isCreative()) {
				if ($damager->getIslandName() === $island->getName()) {
					if ($island->isMember($damager->getCoreUser())) {
						if (!$island->getRole($damager->getCoreUser())->hasPermission(IslandPermissions::PERMISSION_DAMAGE)) {
							$damager->sendMessage(Skyblock::ERROR_PREFIX . "You have no permissions to §cdamage§7.");
							$event->cancel();
						} elseif (!$damager->isCreative()) $event->cancel();
					} else $event->cancel();
				}
			}

			if ($event instanceof EntityDeathEvent && !$event->getEntity() instanceof CorePlayer) {
				$island->addStat(IslandStats::MOBS_KILLED_MANUALLY, IslandStats::XP[IslandStats::MOBS_KILLED_MANUALLY]);
			}
		}
	}



	public function onEntityDeath(EntityDeathEvent $ev): void{
		$world = $ev->getEntity()->getWorld();
		$entity = $ev->getEntity();
		$island = IslandManager::getInstance()->getIslandFromName($world->getFolderName());
		if(is_null($island)) return;
		if ($entity instanceof CorePlayer) return;

		$damageCause = $entity->getLastDamageCause()->getCause();
		switch ($damageCause) {
			case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
			case EntityDamageEvent::CAUSE_PROJECTILE:
				$island->addStat(IslandStats::MOBS_KILLED_AFK, IslandStats::XP[IslandStats::MOBS_KILLED_AFK]);
				break;
		}
	}


	public function onEntityDamage(EntityDamageEvent $ev): void {
		$e = $ev->getEntity();
		if(!$e instanceof CorePlayer) return;
		$world = $e->getWorld();
		$cause = $ev->getCause();

		switch ($cause) {
			case EntityDamageEvent::CAUSE_FALL:
			case EntityDamageEvent::CAUSE_VOID:
				if($cause === EntityDamageEvent::CAUSE_VOID) {
					$e->teleport($world->getSpawnLocation());
				}

				$ev->cancel();
				break;
		}
	}
}