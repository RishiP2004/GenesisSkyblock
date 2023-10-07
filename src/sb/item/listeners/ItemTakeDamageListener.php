<?php

namespace sb\item\listeners;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use sb\player\CorePlayer;

interface ItemTakeDamageListener {
	public function onTakeDamage(Item $item, CorePlayer $damaged, EntityDamageEvent $event) : void;
}
