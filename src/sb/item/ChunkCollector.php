<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\block\Chest;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\item\Item;
use sb\block\tile\ChunkCollector as CTile;
use sb\item\listeners\ItemBlockPlaceListener;
use sb\player\CorePlayer;
use sb\Skyblock;

class ChunkCollector extends CustomItem implements ItemBlockPlaceListener {
	public function getName(): string {
		return "ChunkCollector";
	}

	public function getItem() : Item {
		$item = VanillaBlocks::CHEST()->asItem();
		$item->setCustomName("§r§l§6Chunk Collector §r§7(Place Down)");
		$item->setLore([
			"§r§7Place this down in a chunk",
			"§r§7and every item in the chunk will",
			"§r§7go directly into this §6Chunk Collector!",
		]);

		self::addNameTag($item);
		return $item;
	}

	public function getId() : string {
		return CustomItemIds::CHUNKCOLLECTOR;
	}

	public function onPlaceBlock(Item $item, CorePlayer $player, BlockPlaceEvent $event) : void {
		$block = $event->getTransaction()->getBlocks();

		foreach($block as [, , ,$b]) {
			if($b instanceof Chest) {
				if(!empty(CTile::getCache(CTile::getCacheIdentifier($b->getPosition())))) {
					$player->sendMessage(Skyblock::ERROR_PREFIX . "There's already a chunk chest in this chunk.");
					$event->cancel();
				}
				$world = $b->getPosition()->getWorld();
				$tile = $world->getTile($b->getPosition());
				$tile->close();

				$world->addTile(new CTile($b->getPosition()->getWorld(), $b->getPosition()));
			}
		}
	}
}