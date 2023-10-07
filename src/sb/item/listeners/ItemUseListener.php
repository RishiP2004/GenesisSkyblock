<?php

namespace sb\item\listeners;

use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use sb\player\CorePlayer;

interface ItemUseListener {
	public function onUse(Item $item, CorePlayer $player, PlayerItemUseEvent $event) : void;
}