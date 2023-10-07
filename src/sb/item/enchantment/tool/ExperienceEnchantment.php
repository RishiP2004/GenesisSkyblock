<?php

namespace sb\item\enchantment\tool;

use pocketmine\event\block\BlockBreakEvent;
use sb\item\enchantment\BlockBreakEnchantment;
use sb\item\enchantment\CustomEnchantment;

class ExperienceEnchantment extends CustomEnchantment implements BlockBreakEnchantment {
	public function onBreak(BlockBreakEvent $event, int $level) : void {
		$event->setXpDropAmount($event->getXpDropAmount() / 100 * (4 * $level));
	}
}