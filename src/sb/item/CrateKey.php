<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use sb\block\tile\Crate;
use sb\item\listeners\ItemInteractListener;
use sb\player\CorePlayer;
use sb\Skyblock;
use sb\world\crates\CratesHandler;

class CrateKey extends CustomItem implements ItemInteractListener {
	private string $type;

	public function getName() : string {
		return "Crate Key";
	}

	public function getId() : string {
		return CustomItemIds::CRATEKEY;
	}

	public function getItem(string $type) : Item {
		$this->type = $type;
		$item = VanillaBlocks::TRIPWIRE_HOOK()->asItem();

		$name = CratesHandler::get($type)->getColouredName();

		$item->setCustomName("§r{$name} §r§aKey");
		$item->setLore([
			"§r§7Hit on a {$name} §r§7Crate to open it!",
		]);
		$item->getNamedTag()->setString("type", CratesHandler::get($type)->getName());
		self::addNameTag($item);
		return $item;
	}

	public function onInteract(Item $item, CorePlayer $player, PlayerInteractEvent $event) : void {
		$event->cancel();
		$block = $event->getBlock();
		$world = $block->getPosition()->getWorld();

		if($block->getTypeId() == VanillaBlocks::CHEST()->getTypeId()) {
			$tile = $world->getTile($block->getPosition());
			if($tile instanceof Crate && $tile->getType()->getName() == $this->type) {
				$event->cancel();
				if($event->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
					$loop = 1;

					if($player->isSneaking()) {
						$loop = $item->getCount();
					}
					for($i = 0; $i < $loop; $i++) {
						$tile->open($player);
						$item->pop();
						$player->getInventory()->setItemInHand($item);
					}
				}
			} else {
				$player->sendMessage(Skyblock::ERROR_PREFIX . "You can only use this key on a " . CratesHandler::get($this->type)->getColouredName());
			}
		}
	}
}