<?php

namespace sb\item\enchantment;

use pocketmine\event\block\BlockBreakEvent;

interface BlockBreakEnchantment {
	public function onBreak(BlockBreakEvent $event, int $level) : void;
}