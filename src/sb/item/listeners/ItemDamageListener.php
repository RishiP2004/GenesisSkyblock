<?php

namespace sb\item\listeners;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use sb\player\CorePlayer;

interface ItemDamageListener {
	public function onDamage(Item $item, CorePlayer $damager, EntityDamageEvent $event) : void;
}