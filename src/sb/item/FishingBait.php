<?php

namespace sb\item;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use sb\item\utils\BaitType;

class FishingBait extends CustomItem {
	public function getName() : string {
		return "Fishing Bait";
	}

	public function getId() : string {
		return CustomItemIds::FISHING_BAIT;
	}

	public function getLore(BaitType $type) : array {
		$t = $type->getCustomName();
		return [
			"",
			"§r§7Apply this to a fishing rod",
			"§r§7in order or the fishing loot",
			"§r§7to be focused on {$t} §r§7rewards",
		];
	}

	public function getItem(BaitType $type) : Item {
		$item = VanillaItems::PAPER();
		$custom = $type->getCustomName();

		$item->setCustomName("§l{$custom} §dFishing Bait");
		$item->setLore($this->getLore($type));
		$item->getNamedTag()->setString("baitType", $type->getDisplayName());
		self::addNameTag($item);
		return $item;
	}
}
