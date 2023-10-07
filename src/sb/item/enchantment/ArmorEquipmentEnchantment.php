<?php

namespace sb\item\enchantment;

use pocketmine\player\Player;

abstract class ArmorEquipmentEnchantment extends CustomEnchantment {
	abstract public function onEquip(Player $p, int $level): void;

	abstract public function onRemove(Player $p, int $level): void;
}