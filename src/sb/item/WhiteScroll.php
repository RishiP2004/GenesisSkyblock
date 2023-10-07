<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\world\sound\AnvilUseSound;
use pocketmine\world\sound\XpLevelUpSound;
use sb\item\listeners\ItemInventoryListener;
use sb\item\listeners\ItemUseListener;
use sb\lang\CustomKnownTranslationFactory;
use sb\player\CorePlayer;
use sb\Skyblock;

class WhiteScroll extends CustomItem implements ItemInventoryListener {
	public function getName() : string {
		return "White Scroll";
	}

	public function getId() : string {
		return CustomItemIds::WHITE_SCROLL;
	}

	public function getItem(): Item {
		$scroll = VanillaItems::PAPER();
		$scroll->setCustomName("§r§l§fWHITE SCROLL §r§7(Apply)");
		$scroll->setLore([
			"§r§7Prevents an item from being destroyed",
			"§r§7due to a failed enchantment book",
			"",
			"§r§7drag n' drop to apply this item"

		]);

		self::addNameTag($scroll);
		return $scroll;
	}


	public function onInventoryListen(CorePlayer $player, Item $item, Item $otherItem, SlotChangeAction $action, SlotChangeAction $otherAction, InventoryTransactionEvent $event): void{
		if($item->getTypeId() === VanillaItems::AIR()->getTypeId()) return;
		if($item->getTypeId() === VanillaItems::AIR()->getTypeId()) return;

		if(!$item instanceof Durable){
			$player->sendMessage(TextFormat::colorize("&r&c&l(!) &r&cThis item is not of a durable instance"));
			$player->sendMessage(TextFormat::colorize("&r&7Try an item such as a sword or armor piece"));

			$player->getWorld()->addSound($player->getLocation(), new AnvilFallSound());
			return;
		}
		if(self::hasNbt($item)){
			$player->sendMessage(TextFormat::colorize("&r&c&l(!) &r&cThis item already has a white scroll applied."));
			return;
		}
		$event->cancel();

		$item->setUnbreakable(true);
		$lore = $item->getLore();
		$lore[] = TextFormat::colorize("&r&f&lPROTECTED");
		$item->setLore($lore);
		$otherAction->getInventory()->setItem($otherAction->getSlot(), VanillaItems::AIR());
		$action->getInventory()->setItem($action->getSlot(), $item);

		$player->getWorld()->addSound($player->getLocation(), new AnvilUseSound());

	}

	public static function hasNbt(Item $item): bool{
		return $item->getNamedTag()->getString("whiteScroll", "") !== "";
	}
}