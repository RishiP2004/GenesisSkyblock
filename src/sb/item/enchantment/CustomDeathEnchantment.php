<?php

namespace sb\item\enchantment;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDeathEvent;

interface CustomDeathEnchantment {
	public function onDeath(Entity $killer, Entity $victim, int $enchantmentLevel, EntityDeathEvent $event): void;
}