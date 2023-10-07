<?php

namespace sb\item\enchantment;

use pocketmine\entity\Entity;

interface CustomArmorEnchantment {
	public function onDamaged(Entity $victim, Entity $attacker, int $enchantmentLevel): void;
}