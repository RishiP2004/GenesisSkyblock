<?php

namespace sb\item\enchantment\tool;

use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\VanillaItems;
use sb\item\enchantment\BlockBreakEnchantment;
use sb\item\enchantment\CustomEnchantment;

class AutoSmeltEnchantment extends CustomEnchantment implements BlockBreakEnchantment {
	public function onBreak(BlockBreakEvent $event, int $level) : void {
		$newDrops = $event->getDrops();
		foreach ($newDrops as $k => $drop) {
			$item = match ($drop->getTypeId()) {
				BlockTypeIds::IRON_ORE => VanillaItems::IRON_INGOT(),
				BlockTypeIds::GOLD => VanillaItems::GOLD_INGOT(),
				BlockTypeIds::COBBLESTONE => VanillaBlocks::COBBLESTONE()->asItem(),
				default => null
			};
			if ($item !== null) {
				$newDrops[$k] = $item;
			}
		}

		$event->setDrops($newDrops);
	}
}