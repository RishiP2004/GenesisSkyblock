<?php

namespace sb\item\enchantment\weapon;

use pocketmine\entity\Entity;
use sb\item\enchantment\CustomEnchantment;
use sb\item\enchantment\CustomMeleeWeaponEnchantment;
use sb\item\enchantment\utils\EnchantCooldownTrait;

class ExecuteEnchantment extends CustomEnchantment implements CustomMeleeWeaponEnchantment {
	use EnchantCooldownTrait;

	public function onDamage(Entity $attacker, Entity $victim, int $enchantmentLevel, float $finalDamage) : void {
		if(mt_rand(1, 100) < $enchantmentLevel*2) {
			$victim->setHealth($attacker->getHealth() - ($dmg = mt_rand(1, 3)));
			$attacker->sendMessage("§r§l§b** Execute (§r§7+$dmg Outgoing Damage§l§b) **");
			$this->setCooldown($attacker, 2);
		}
	}
}