<?php

namespace sb\islands\upgrades;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use sb\islands\traits\IslandCallTrait;

abstract class IslandUpgrade {
	use IslandCallTrait;

    private int $maxLevel;

    public function __construct(
		private string $name,
		private string $description,
		private array $upgradePrices,
		private int $amountContributed,
		private int $currentLevel,
		private string $islandId
	) {
        $this->maxLevel = count($upgradePrices);
    }

    public final function getName() : string {
        return $this->name;
    }

    public final function getDescription() : string {
        return $this->description;
    }

    public final function getUpgradePrices() : array {
        return $this->upgradePrices;
    }

    public final function getMaxLevel() : int {
        return $this->maxLevel;
    }

    public final function getAmountContributed() : int {
        return $this->amountContributed;
    }

    public final function getCurrentLevel() : int {
        return $this->currentLevel;
    }

    public final function getIslandId() : string {
        return $this->islandId;
    }

    public final function getNextUpgradePrice() : int {
        return $this->upgradePrices[$this->currentLevel + 1] ?? -1;
    }

    public function contribute(int $amount) : void {
		$this->amountContributed += $amount;

		if($this->amountContributed >= $this->getNextUpgradePrice()) {
			var_dump("Upgraded to level " . $this->currentLevel + 1);
			$this->currentLevel+=1;
			$this->amountContributed = 0;
		}
    }
	//todo: change these to be interfaced 
	public abstract function interceptBreak(BlockBreakEvent $event) : void;

    public abstract function interceptPlace(BlockPlaceEvent $event) : void;
	//Todo: update when more added
	public static function unserialize(string $to) : IslandUpgrade {
		$to = unserialize($to);

		switch($to["name"]) {
			case "Size":
				return new IslandSizeUpgrade($to["contributed"], $to["level"], $to["island"]);
			break;
			case "Member Size":
				return new IslandMemberSizeUpgrade($to["contributed"], $to["level"], $to["island"]);
			break;
			case "Hopper Limit":
				return new IslandHopperLimitUpgrade($to["contributed"], $to["level"], $to["hoppersPlaced"], $to["island"]);
			break;
			case "Spawner Limit":
				return new IslandSpawnerLimitUpgrade($to["contributed"], $to["level"], $to["spawnersPlaced"], $to["island"]);
			break;
		}
	}

	public abstract function serialize() : string;
}