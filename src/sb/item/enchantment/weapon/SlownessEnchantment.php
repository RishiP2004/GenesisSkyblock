<?php

namespace sb\item\enchantment\weapon;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use sb\item\enchantment\CustomEnchantment;
use sb\item\enchantment\CustomMeleeWeaponEnchantment;
use sb\item\enchantment\utils\EnchantCooldownTrait;

class SlownessEnchantment extends CustomEnchantment implements CustomMeleeWeaponEnchantment {
	use EnchantCooldownTrait;

	public function onDamage(Entity $attacker, Entity $victim, int $enchantmentLevel, float $finalDamage) : void {
		if(mt_rand(1, 100) < $enchantmentLevel*5) {
			$victim->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20*5, 4));
			$this->setCooldown($attacker, 60);
		}
	}
}