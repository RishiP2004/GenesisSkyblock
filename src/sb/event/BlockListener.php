<?php

namespace sb\event;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\ItemSpawnEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemBlock;
use pocketmine\item\Pickaxe;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\ExplodeParticle;
use pocketmine\world\sound\ExplodeSound;
use sb\block\tile\Crate;
use sb\block\tile\IslandCache;

use sb\block\tile\MonsterSpawner;
use sb\item\CustomItems;
use sb\player\CorePlayer;
use sb\Skyblock;
use sb\world\islandCache\IslandCacheHandler;

class BlockListener implements Listener {
	public function onPlace(BlockPlaceEvent $event) {
		if($event->isCancelled())return;

		$item = $event->getItem();

		if(!$item instanceof ItemBlock) return;

		$block = $item->getBlock();
		if(
			!$block instanceof MonsterSpawner or
			$block instanceof \pocketmine\block\MonsterSpawner
		){
			return;
		}
		$transaction = $event->getTransaction();

		foreach($transaction->getBlocks() as [$x, $y, $z, $blocks]){
			$transaction->addBlock($blocks->getPosition(), CustomItems::MONSTER_SPAWNER()->setLegacyEntityId(CustomItems::getSpawnerEntityId($item)));
		}
	}

	public function onSpawnerBreak(BlockBreakEvent $event){
		if($event->isCancelled()){
			return;
		}
		$item = $event->getItem();
		$tile = ($position = $event->getBlock()->getPosition())->getWorld()->getTile($position);
		if(
			!$tile instanceof MonsterSpawner or
			!$item instanceof Pickaxe or
			!$item->hasEnchantment(VanillaEnchantments::SILK_TOUCH())
		){
			return;
		}
		$event->setDrops([StringToItemParser::getInstance()->parse('52:'. $tile->getLegacyEntityId()) ?? CustomItems::MONSTER_SPAWNER()->asItem()]);
	}

	public function onInteract(PlayerInteractEvent $event) : void {
		/**@var $player CorePlayer */
		$player = $event->getPlayer();
		$block = $event->getBlock();

		if($block->getTypeId() == VanillaBlocks::CHEST()->getTypeId()) {
			$tile = $block->getPosition()->getWorld()->getTile($block->getPosition());

			if($tile instanceof Crate) {
				$event->cancel();

				if($event->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK) {
					$tile->previewRewards($player);
				} else {
					$player->sendMessage(Skyblock::ERROR_PREFIX . "You need a " . $tile->getType()->getColouredName() . TextFormat::RESET . TextFormat::RED . " key to use this Crate");
				}
			}
			if($tile instanceof IslandCache) {
				$event->cancel();

				$t = mt_rand(3, 9);

				for($i = 1; $i < $t; $i++) {
					$reward = IslandCacheHandler::getItem()->getItem();

					$player->getInventory()->canAddItem($reward) ? $player->getInventory()->addItem($reward) : $player->getWorld()->dropItem($player->getPosition()->asVector3(), $reward);
				}
				$tile->close();
				$block->getPosition()->getWorld()->addSound($block->getPosition()->add(0, 1, 0), new ExplodeSound());
				$block->getPosition()->getWorld()->addParticle($block->getPosition()->add(0, 1, 0), new ExplodeParticle());
				$block->getPosition()->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
			}
		}
	}

	public function onItemEntitySpawn(ItemSpawnEvent $event) : void {
		$e = $event->getEntity();
		$e->setScale(2);

		if ($e->isFlaggedForDespawn()) return;
		if ($e->isClosed()) return;

		$identifier = \sb\block\tile\ChunkCollector::getCacheIdentifier($e->getPosition());

		if(!empty(\sb\block\tile\ChunkCollector::$chunkCache[strtolower($identifier)])) {
			foreach (\sb\block\tile\ChunkCollector::$chunkCache[strtolower($identifier)] as $tile) {
				if($tile->isClosed()) continue;

				if($tile->getInventory()->canAddItem($e->getItem())) {
					$tile->getInventory()->addItem($e->getItem());
					$e->flagForDespawn();
					break;
				}
			}
		}
	}

	public function onBreak(BlockBreakEvent $event) {
		$block = $event->getBlock();

		if($block->getTypeId() == VanillaBlocks::CHEST()->getTypeId()) {
			$tile = $block->getPosition()->getWorld()->getTile($block->getPosition());

			if($tile instanceof \sb\block\tile\ChunkCollector) {
				$arr = [];
				$added = false;
				foreach($event->getDrops() as $drop) {
					if(!$added && $drop->getTypeId() == VanillaBlocks::CHEST()->asItem()->getTypeId()) {
						$drop->pop();
						$added = true;
						$arr[] = CustomItems::CHUNKCOLLECTOR()->getItem();
					}
					if(!$drop->isNull()) {
						$arr[] = $drop;
					}
				}
				$event->setDrops($arr);
			}
			if($tile instanceof Crate) {
				$event->cancel();
			}
		}
	}
}