<?php

namespace sb\item\enchantment\weapon;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\player\Player;
use sb\item\enchantment\CustomDeathEnchantment;
use sb\item\enchantment\CustomEnchantment;

class InquisitiveEnchantment extends CustomEnchantment implements CustomDeathEnchantment {
	public function onDeath(Entity $killer, Entity $victim, int $enchantmentLevel, EntityDeathEvent $event) : void {
		if(!$victim instanceof Player) return;

		$event->setXpDropAmount((int)round($event->getXpDropAmount() * ((0.5 * $enchantmentLevel) + 1)));
	}
}