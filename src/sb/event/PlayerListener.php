<?php

declare(strict_types = 1);

namespace sb\event;

use kingofturkey38\voting38\events\PlayerVoteEvent;
use pocketmine\event\block\{
	BlockBreakEvent,
	BlockPlaceEvent,
	SignChangeEvent};
use pocketmine\event\entity\{
	EntityCombustEvent,
	EntityDamageByEntityEvent,
	EntityDamageEvent,
	EntityDeathEvent,
	EntityEffectEvent,
	EntityShootBowEvent};
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\inventory\{
	CraftItemEvent,
	InventoryOpenEvent,
	InventoryTransactionEvent};
use pocketmine\event\Listener;
use pocketmine\event\player\{
	PlayerBucketEvent,
	PlayerChatEvent,
	PlayerCreationEvent,
	PlayerDropItemEvent,
	PlayerExhaustEvent,
	PlayerInteractEvent,
	PlayerItemConsumeEvent,
	PlayerItemHeldEvent,
	PlayerJoinEvent,
	PlayerLoginEvent,
	PlayerMoveEvent,
	PlayerQuitEvent};
use pocketmine\event\server\CommandEvent;
use pocketmine\inventory\PlayerInventory;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use sb\player\CorePlayer;
use sb\player\CoreUser;
use sb\player\level\LevelHandler;
use sb\player\PlayerData;
use sb\player\PlayerManager;
use sb\player\rank\RankChatter;
use sb\player\traits\PlayerCallTrait;
use sb\scheduler\player\CombatTagTask;
use sb\server\VoteHandler;
use sb\Skyblock;

class PlayerListener implements Listener {
	use PlayerCallTrait;

	/**
	 * @param PlayerVoteEvent $event
	 * @priority LOW
	 */
	/**
	public function onVote(PlayerVoteEvent $event): void {
		/** @var CorePlayer $player */
	/**
		$player = $event->getPlayer();

		$event->setGiveRewards(false);
		ServerManager::getInstance()->vote($player);
	}*/

	public function onCommandPreprocess(CommandEvent $event) {
		$player = $event->getSender();
		$msg = $event->getCommand();

		if(!$player instanceof CorePlayer) return;
		if (!$player->isCombatTagged()) return;

		$msg = explode(" ", $msg);
		if(!in_array($msg[0], PlayerData::BANNED_COMMANDS)) return;
		$player->sendMessage(Skyblock::ERROR_PREFIX . "Cannot use this command in Combat!");
		$event->cancel();
	}

	public function onPlayerBucket(PlayerBucketEvent $event) : void {
        $player = $event->getPlayer();

        if($player instanceof CorePlayer) {
			if(!$player->isInitialized()) {
				$event->cancel();
			}
			if($player->isInStaffMode()) {
				$event->cancel();
				$player->sendMessage(Skyblock::ERROR_PREFIX . "You cannot use buckets in Staff Mode.");
			}
        }
    }

    public function onPlayerCreation(PlayerCreationEvent $event) {
        $event->setPlayerClass(CorePlayer::class);
    }

    public function onPlayerExhaust(PlayerExhaustEvent $event) : void {
        $player = $event->getPlayer();

        if($player instanceof CorePlayer) {
        	if(!$player->isInitialized()) {
        		$event->cancel();
			}
			if($player->isInStaffMode()) {
				$event->cancel();
			}
        }
    }

	public function onPlayerChat(PlayerChatEvent $event) : void {
		$player = $event->getPlayer();

		if($player instanceof CorePlayer) {
			if(!$player->isInitialized()) {
				$event->cancel();
			}
			$event->setFormatter(new RankChatter($player));
		}
	}

    public function onPlayerInteract(PlayerInteractEvent $event) : void {
        $player = $event->getPlayer();
		$item = $event->getItem();

        if($player instanceof CorePlayer) {
			if(!$player->isInitialized()) {
				$event->cancel();
			}
        }
    }
	
	public function onPlayerItemHeld(PlayerItemHeldEvent $event) : void{
		/** @var CorePlayer $player */
		$player = $event->getPlayer();
		if (!$player->isInitialized()) {
			$event->cancel();
		}
		$player->lastHeldSlot = $event->getSlot(); //bro???
	}

	public function onPlayerItemConsume(PlayerItemConsumeEvent $event) : void {
    	$player = $event->getPlayer();

    	if($player instanceof CorePlayer) {
    		if(!$player->isInitialized()) {
    			$event->cancel();
			}
		}
	}

    public function onPlayerLogin(PlayerLoginEvent $event)  : void {
        $player = $event->getPlayer();

        if($player instanceof CorePlayer) {
			$this->getCoreUser($player->getXuid(), function(?CoreUser $user) use($player, $event) {
				if(is_null($user)) {
					PlayerManager::getInstance()->register($player);
				}
				if(!$player->isInitialized()) {
					$player->coreUser = $user;
				}
			});
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();

        if($player instanceof CorePlayer) {
			$player->join($player->getCoreUser());
			$player->sendTitle(TextFormat::colorize("&r&l&bSkyblock"), TextFormat::colorize("&r&7&oWelcome, " . $player->getName()));

			$player->sendMessage(join("\n", [
				"§l§c§o     §r",
				"§r§l§bSkyblock §r§f| §r§l§bSaturn",
				"§r§fWelcome §r§7".$player->getName()."§r to §r§l§bSaturn!",
				"§l§c§o     §r",
				"§r§l§bStore: §rshop.genesis.com",
				"§r§l§bForums: §rforums.genesis.com",
				"§r§l§bDiscord: §rdiscord.gg/cosmicpe",
				"§r§l§bVote: §rvote.genesis.com",
				"§l§c§o     §r",
			]));
		}
	}

	public function onDamage(EntityDamageEvent $event): void{
		$entity = $event->getEntity();

		if ($event->isCancelled()) return;

		if ($event instanceof EntityDamageByEntityEvent) {
			$damager = $event->getDamager();

			if ($event->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK || $event->getCause() === EntityDamageEvent::CAUSE_PROJECTILE) {
				if ($entity instanceof CorePlayer && $damager instanceof CorePlayer) {
					$entity->combatTag();
					$damager->combatTag();

					if (!$entity->isCombatTagged() && !$damager->isCombatTagged()) {
						Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new CombatTagTask($damager, $entity), 20);
						Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new CombatTagTask($entity, $damager), 20);
					}
				}
			}
			if ($event instanceof EntityDeathEvent) {
				if ($damager->isCombatTagged()) {
					$entity->sendMessage(Skyblock::ERROR_PREFIX . "You have left combat. You may now safely logout.");
					$entity->combatTag(false);
				}
			}
		}
	}

    public function onPlayerMove(PlayerMoveEvent $event) : void {
        $player = $event->getPlayer();

        if($player instanceof CorePlayer) {
			if(!$player->isInitialized()) {
				return;
			}
			if($player->hasNoClientPredictions()) {
				$player->sendPopup(Skyblock::ERROR_PREFIX . "You can't move while you're frozen!");
				$event->cancel();
			}
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();

        if($player instanceof CorePlayer) {
            $player->leave();
        }
    }

	public function onDrop(PlayerDropItemEvent $event) : void {
		$player = $event->getPlayer();

		if($player instanceof CorePlayer) {
			if(!$player->isInitialized()) {
				return;
			}
			if($player->isInStaffMode()) {
				$event->cancel();
				$player->sendMessage(Skyblock::ERROR_PREFIX . "You cannot drop items in staff mode");
			}
		}
	}

	public function onEntityCombust(EntityCombustEvent $event) : void {
		$player = $event->getEntity();

		if($player instanceof CorePlayer) {
			if(!$player->isInitialized()) {
				$event->cancel();
			}
			if(!$player->isInStaffMode()) {
				$event->cancel();
			}
		}
	}

    public function onEntityDamage(EntityDamageEvent $event) : void {
        $entity = $event->getEntity();
		//deny mob attack maybe
        if($entity instanceof CorePlayer) {
        	$player = $entity;
			
			if(!$player->isInitialized()) {
				$event->cancel();
			}
			if ($player->hasNoClientPredictions() or $player->isInStaffMode()) {
				$event->cancel();
			}
            if($event instanceof EntityDamageByEntityEvent) {
				$damager = $event->getDamager();
				
				if($damager instanceof CorePlayer) {
					if(!$damager->isInitialized()) {
						$event->cancel();
					}
					if($damager->isInStaffMode()) {
						$event->cancel();

						$entity = $event->getEntity();
						$item = $damager->getInventory()->getItemInHand();

						$nbt = $item->getNamedTag();

						if($nbt->getString("Freeze", "") !== "") {
							$entity->setFrozen(!$entity->isFrozen());

							if($entity->isFrozen()) {
								$entity->sendMessage(Skyblock::PREFIX . "You have been Frozen!");
								$damager->sendMessage(Skyblock::PREFIX . "You have frozen " . $entity->getName()());

							} else {
								$entity->sendMessage(Skyblock::PREFIX . "You have been Unfrozen!");
								$damager->sendMessage(Skyblock::PREFIX . "You have Unfrozen " . $entity->getName()());
							}
						}
						if($nbt->getString("Invsee", "") !== "") {
							Server::getInstance()->dispatchCommand($damager, "invsee \"" . $entity->getName() . "\"");
						}
					}
				}
			}
		}
	}

	public function onEntityEffect(EntityEffectEvent $event) : void {
    	$player = $event->getEntity();

    	if($player instanceof CorePlayer) {
    		if(!$player->isInitialized()) {
    			$event->cancel();
    		}
    	}
	}

	public function onEntityShootBow(EntityShootBowEvent $event) : void {
		$player = $event->getEntity();

		if($player instanceof CorePlayer) {
			if(!$player->isInitialized()) {
				$event->cancel();
			}
		}
	}

	public function onSignChange(SignChangeEvent $event) : void {
		$player = $event->getPlayer();

		if($player instanceof CorePlayer) {
			if(!$player->isInitialized()) {
				$event->cancel();
			}
		}
	}

	public function onBlockBreak(BlockBreakEvent $event) : void {
		$player = $event->getPlayer();

		if($player instanceof CorePlayer) {
			if(!$player->isInitialized()) {
				$event->cancel();
			}
			foreach(LevelHandler::getAll() as $lvl) $lvl->handle($player, $event);
		}
	}

	public function onBlockPlace(BlockPlaceEvent $event) : void {
		$player = $event->getPlayer();

		if($player instanceof CorePlayer) {
			if(!$player->isInitialized()) {
				$event->cancel();
			}
		}
	}

	public function onCraftItem(CraftItemEvent $event) : void {
		$viewer = $event->getPlayer();

		if($viewer instanceof CorePlayer) {
			if(!$viewer->isInitialized()) {
				$event->cancel();
			}
		}
	}

	public function onInventoryOpen(InventoryOpenEvent $event) : void {
		$inventory = $event->getInventory();

		if($inventory instanceof PlayerInventory) {
			$player = $inventory->getHolder();

			if($player instanceof CorePlayer) {
				if(!$player->isInitialized()) {
					$event->cancel();
				}
			}
		}
	}

	public function onInventoryPickupArrow(EntityItemPickupEvent $event) : void {
		$entity = $event->getEntity();

		if($entity instanceof CorePlayer) {
			if(!$entity->isInitialized()) {
				$event->cancel();
			}
			if($entity->isInStaffMode()) {
				$event->cancel();
				$entity->sendMessage(Skyblock::ERROR_PREFIX . "You cannot drop items in staff mode");
			}
		}
	}

	public function onInventoryTransaction(InventoryTransactionEvent $event) : void {
		$source = $event->getTransaction()->getSource();

		if($source instanceof CorePlayer) {
			if(!$source->isInitialized()) {
				$event->cancel();
			}
			if($source->isInStaffMode()) {
				$event->cancel();
			}
		}
	}
}
