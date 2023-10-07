<?php

declare(strict_types = 1);

namespace sb\server\shop\entry;

use pocketmine\item\Item;
use sb\player\CorePlayer;

abstract class BaseEntry {
	public function __construct(private readonly string $name, private readonly Item $item) {}

	public function getItem() : Item {
		return $this->item;
	}

	public function getName() : string {
		return $this->name;
	}

	public abstract function sendFinal(CorePlayer $player) : void;
}