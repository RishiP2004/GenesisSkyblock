<?php

namespace sb\item\enchantment\utils;

use pocketmine\player\Player;

trait EnchantCooldownTrait{
	/**@var string[] $cooldowns */
	private array $cooldowns = [];

	/**
	 * @param Player $player
	 * @return int
	 */
	public function getCooldown(Player $player): int{
		return $this->cooldowns[$player->getName()] ?? 0;
	}

	/**
	 * @param Player $player
	 * @param int $time
	 * @return void
	 */
	public function setCooldown(Player $player, int $time): void {
		if(isset($this->cooldowns[$player->getName()])) return;

		$this->cooldowns[$player->getName()] = time() + $time;
	}
}