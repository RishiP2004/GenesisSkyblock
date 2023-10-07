<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;

class KeyPouch extends CustomItem {
	public static function getItem(int $min, int $max): Item {
	}

	public function onUse(Player $player, Event $event, Item $item) : void{
	}

	public function getName() : string {
		return "KeyPouch";
	}
}