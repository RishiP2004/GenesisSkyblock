<?php

namespace sb\utils;

use pocketmine\item\Item;

class SimpleReward {
	public function __construct(
		private readonly string $name,
		private readonly array $items,
		private readonly array $cmds
	) {}

	public function getName() : string {
		return $this->name;
	}
	/**
	 * @return Item[]
	 */
	public function getItems() : array {
		return $this->items;
	}

	public function getCmds() : array {
		return $this->cmds;
	}
}