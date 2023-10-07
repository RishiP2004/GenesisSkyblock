<?php

namespace sb\item\enchantment;

use pocketmine\player\Player;

interface ItemHeldEnchantment {
	public function onHeld(Player $p, int $level): void;

	public function onUnHeld(Player $p, int $level): void;
}