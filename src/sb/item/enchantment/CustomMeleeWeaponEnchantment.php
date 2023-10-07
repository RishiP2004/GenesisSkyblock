<?php

namespace sb\item\enchantment;

use pocketmine\entity\Entity;

interface CustomMeleeWeaponEnchantment {
	public function onDamage(Entity $attacker, Entity $victim, int $enchantmentLevel, float $finalDamage): void;
}