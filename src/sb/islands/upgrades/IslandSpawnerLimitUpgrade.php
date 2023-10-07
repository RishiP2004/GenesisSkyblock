<?php

namespace sb\islands\upgrades;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use sb\islands\traits\IslandCallTrait;
use sb\Skyblock;

class IslandSpawnerLimitUpgrade extends IslandUpgrade {
	use IslandCallTrait;

	public function __construct(int $amountContributed, int $currentLevel, private int $spawnersPlaced, string $islandID) {
		parent::__construct(
			"Spawner Limit",
			"Upgrade the Spawner limit of your island.",
			[1000, 2000, 3000, 4000, 5000],
			$amountContributed,
			$currentLevel,
			$islandID
		);
	}

	public function getSpawnersPlaced() : int {
		return $this->spawnersPlaced;
	}

	public function setSpawnersPlaced(int $amount) : void {
		$this->spawnersPlaced = $amount;
	}

	public function interceptBreak(BlockBreakEvent $event) : void {}

	public function interceptPlace(BlockPlaceEvent $event) : void {
		$player = $event->getPlayer();

		foreach($event->getTransaction()->getBlocks() as [$x, $y, $z, $block]) {
			if($block->getTypeId() == VanillaBlocks::MONSTER_SPAWNER()->getTypeId()) { //todo: check if custom spawners has same blocktypeid
				if ($this->getSpawnersPlaced() > $this->getCurrentLevel() * 10) {
					$event->cancel();
					$player->sendMessage(Skyblock::ERROR_PREFIX . "You must upgrade your island size in order to place more spawners.");
				} else $this->setSpawnersPlaced($this->getSpawnersPlaced() + 1);
			}
		}
	}

	public function serialize() : string {
		return serialize([
			"name" => $this->getName(),
			"contributed" => $this->getAmountContributed(),
			"level" => $this->getCurrentLevel(),
			"spawnersPlaced" => $this->getSpawnersPlaced(),
			"island" => $this->getCurrentLevel()
		]);
	}

	public function upgraded(): void
	{
		// TODO: Implement upgraded() method.
	}
}