<?php

declare(strict_types=1);

namespace sb\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;

final class PlayerFishEvent extends PlayerEvent implements Cancellable {
	use CancellableTrait;

	public function __construct(
		protected Player $player,
		protected Item $fishingRod,
		protected Item $loot,
		protected int $experience
	) {}

	public function getPlayer() : Player {
		return $this->player;
	}

	public function setPlayer(Player $player) : void {
		$this->player = $player;
	}

	public function getFishingRod(): Item {
		return $this->fishingRod;
	}

	public function setFishingRod(Item $fishingRod) : void {
		$this->fishingRod = $fishingRod;
	}

	public function getLoot() : Item {
		return $this->loot;
	}

	public function setLoot(Item $loot) : void {
		$this->loot = $loot;
	}

	public function getExperience(): int {
		return $this->experience;
	}

	public function setExperience(int $experience): void {
		$this->experience = $experience;
	}
}