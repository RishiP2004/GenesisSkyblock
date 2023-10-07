<?php

namespace sb\islands\upgrades;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\math\Vector3;

class IslandMemberSizeUpgrade extends IslandUpgrade {
    public function __construct(int $amountContributed, int $currentLevel, string $islandID) {
        parent::__construct(
            "Member Size",
            "Upgrade the size (players) of your island.",
            [1000, 2000, 3000, 4000, 5000],
            $amountContributed,
            $currentLevel,
            $islandID
        );
    }

	public function interceptBreak(BlockBreakEvent $event) : void {}

	public function interceptPlace(BlockPlaceEvent $event) : void {}

	public final function getMaxMembers(): int{
		var_dump("LEVEL:" . $this->getCurrentLevel());
		return match ($this->getCurrentLevel()) {
			0 => 5,
			1 => 10,
			2 => 15,
			3 => 20,
			4 => 25,
		};
	}

	public function serialize() : string {
		return serialize([
			"name" => $this->getName(),
			"contributed" => $this->getAmountContributed(),
			"level" => $this->getCurrentLevel(),
			"island" => $this->getCurrentLevel()
		]);
	}
}