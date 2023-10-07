<?php

namespace sb\item;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use sb\item\listeners\ItemUseListener;
use sb\item\utils\LootboxType;
use sb\player\CorePlayer;
use pocketmine\utils\TextFormat as T;

class Lootbox extends CustomItem implements ItemUseListener {
	public const TYPE = "type";

	public function getName(): string {
		return "Lootbox";
	}

	public function getItem(LootboxType $type): Item{
		$item = VanillaBlocks::CHEST()->asItem();

		$item->setCustomName(T::colorize("&r&f&lLootbox&r&f: &r" . $type->getCustomName()));
		$item->setLore($type->getLore());
		$item->getNamedTag()->setString(self::TYPE, $type->getDisplayName());
		self::addNameTag($item);

		return $item;
	}

	public function onUse(Item $item, CorePlayer $player, PlayerItemUseEvent $event): void{
		$event->cancel();
		$type = LootboxType::fromString($item->getNamedTag()->getString(self::TYPE, ""));

		$item->pop();
		$player->getInventory()->setItemInHand($item);
	}

	public function getId(): string{
		return CustomItemIds::LOOTBOX;
	}
}