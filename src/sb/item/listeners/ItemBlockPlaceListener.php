<?php

namespace sb\item\listeners;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\item\Item;
use sb\player\CorePlayer;

interface ItemBlockPlaceListener {
	public function onPlaceBlock(Item $item, CorePlayer $player, BlockPlaceEvent $event) : void;
}