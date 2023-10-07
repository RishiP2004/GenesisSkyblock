<?php

namespace sb\utils;

use pocketmine\item\Item;

class Reward {
	public function __construct(private readonly Item $item, private $callable, private readonly int $chance) {}

	public function getItem() : Item {
		return $this->item;
	}

	public function getCallback() : callable {
		return $this->callable;
	}

	public function getChance(): int{
		return $this->chance;
	}
}