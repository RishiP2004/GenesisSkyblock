<?php

namespace sb\player\level;

use sb\player\CorePlayer;

abstract class Level {
	public array $levels = [];

	public function __construct(
		private readonly string $name,
		private readonly int $startXp,
		private readonly int $minLvl,
		private readonly int $maxLvl,
		private readonly float $multiplier
	) {
		$current = $this->startXp;
		for ($i = $this->minLvl; $i <= $this->maxLvl; $i++) {
			if($i === $this->minLvl){
				$this->levels[$i] = $current;
			} else {
				$current *= $this->multiplier;
				$this->levels[$i] = (int) ceil($current);
			}
		}
	}

	public final function getName() : string {
		return $this->name;
	}

	public final function getStartXp() : int {
		return $this->startXp;
	}

	public final function getMinLvl() : int {
		return $this->minLvl;
	}

	public final function getMaxLvl() : int {
		return $this->maxLvl;
	}

	public final function getMultiplier() : int {
		return $this->multiplier;
	}

	public final function getXpNeededFor(int $lvl): int {
		return $this->levels[$lvl] ?? 100;
	}

	public abstract function onXpGained(CorePlayer $player, int $amount): void;

	public abstract function getRewards() : array;

	public function onLevelup(CorePlayer $player, int $oldLvl, int $newLvl): void {
		$player->getCoreUser()->setLevelXp($this, 0);
		$player->getCoreUser()->setLevel($this, $newLvl);

		foreach($this->getRewards() as $reward) {
			$player->getInventory()->canAddItem($reward) ? $player->getInventory()->addItem($reward) : $player->getWorld()->dropItem($player->getPosition()->asVector3(), $reward);
		}
	}

	public function addXp(CorePlayer $player, int $amount) : void {
		$user = $player->getCoreUser();

		if($user->getLevel($this) === $this->getMaxLvl()) return;

		$user->setLevelXp($this, $player->getCoreUser()->getLevelXp($this) + $amount);

		if($user->getLevelXp($this) >= ($level = $this->getXpNeededFor($user->getLevel($this)))) {
			$this->onLevelup($player, $level, $level + 1);
		} else {
			$this->onXpGained($player, $amount);
		}
	}
}