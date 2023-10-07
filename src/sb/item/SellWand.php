<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\block\tile\Chest;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use sb\item\listeners\ItemInteractListener;
use sb\player\CorePlayer;
use sb\server\shop\ShopHandler;
use sb\Skyblock;

class SellWand extends CustomItem implements ItemInteractListener {
	public function getName() : string {
		return "Sell Wand";
	}

	public function getId() : string {
		return CustomItemIds::SELLWAND;
	}

	public function getItem(int $uses, int $totalSold = 0, int $soldChests = 0) : Item {
		$item = VanillaItems::BLAZE_ROD();
		$gain = number_format($totalSold);
		$chest = number_format($soldChests);
		$us = number_format($uses);

		$item->setCustomName("§r§l§cSell Wand (§r§c$uses uses§l) §r§7(Tap on Chest)");
		$item->setLore([
			"§r§7Hit Me on a Chest to sell all the valuable",
			"§r§7items in a chest! Only works if they're sellable!",
			"§r",
			"§r§l§cUSES",
			"§r§c§l * §r§c$us Uses",
			"§r",
			"§r§l§cSTATS",
			"§r§c§l * §r§cSold $" . $gain . " worth of items",
			"§r§c§l * §r§cUsed on $chest chests",
			"§r§cfor more uses.",
		]);
		$item->getNamedTag()->setString("uniqueId", uniqid("" . mt_rand(1, 10)));
		$item->getNamedTag()->setInt("uses", $uses);
		$item->getNamedTag()->setInt("chests", $soldChests);
		$item->getNamedTag()->setString("money", (string) $totalSold);

		self::addNameTag($item);

		return $item;
	}

	public function onInteract(Item $item, CorePlayer $player, PlayerInteractEvent $event) : void {
		$event->cancel();
		$inventory = null;

		$block = $event->getBlock();

		if($block->getTypeId() == VanillaBlocks::CHEST()->getTypeId()) {
			$tile = $block->getPosition()->getWorld()->getTile($block->getPosition()->asVector3());

			if($tile instanceof Chest) {
				$inventory = $tile->getInventory();
			}
		}
		if($inventory === null) {
			$player->sendMessage(Skyblock::ERROR_PREFIX . "You can only use sell wands on chests.");
			return;
		}
		self::sellInventoryWithSellWand($player, $item, $inventory);
	}

	public function sellInventoryWithSellWand(CorePlayer $player, Item $item, Inventory $inventory): void {
		$gain = ShopHandler::sellInventory($player, $inventory);

		if($gain <= 0) {
			$player->sendMessage(Skyblock::PREFIX . "Nothing sellable was found in the chest");
			return;
		}
		$player->sendMessage(Skyblock::PREFIX . "Sold all sellable items in the inventory for §c$" . number_format($gain));

		$uses = $item->getNamedTag()->getInt("uses");
		$current = (int) $item->getNamedTag()->getString("money", "0");

		if($item->getNamedTag()->getInt("sold", -1) !== -1){
			$current = $item->getNamedTag()->getInt("sold");
			$item->getNamedTag()->removeTag("sold");
		}
		$chests = $item->getNamedTag()->getInt("chests");

		if(--$uses <= 0){
			$player->getInventory()->clear($player->getInventory()->getHeldItemIndex());
			$player->sendMessage(Skyblock::ERROR_PREFIX . "The sell wand ran out of uses and broke");
			return;
		}
		$player->getInventory()->setItemInHand($this->getItem($uses, $current + $gain, ++$chests));
	}
}