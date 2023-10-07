<?php

namespace sb\item\listeners;

use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use sb\player\CorePlayer;

interface ItemInteractListener {
	public function onInteract(Item $item, CorePlayer $player, PlayerInteractEvent $event) : void;
}