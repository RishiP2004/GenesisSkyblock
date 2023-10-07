<?php

namespace sb\islands\upgrades;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use sb\islands\traits\IslandCallTrait;
use sb\Skyblock;

class IslandHopperLimitUpgrade extends IslandUpgrade {
	use IslandCallTrait;

    public function __construct(int $amountContributed, int $currentLevel, private int $hoppersPlaced, string $islandID) {
        parent::__construct(
            "Hopper Limit",
            "Upgrade the Hopper limit of your island.",
            [1000, 2000, 3000, 4000, 5000],
            $amountContributed,
            $currentLevel,
            $islandID
        );
    }

	public function getHoppersPlaced() : int {
		return $this->hoppersPlaced;
	}

	public function setHoppersPlaced(int $amount) : void {
		$this->hoppersPlaced = $amount;
	}

	public function interceptBreak(BlockBreakEvent $event) : void {}

    public function interceptPlace(BlockPlaceEvent $event) : void {
        $player = $event->getPlayer();

		foreach($event->getTransaction()->getBlocks() as [$x, $y, $z, $block]) {
			if($block->getTypeId() == VanillaBlocks::HOPPER()->getTypeId()) { //todo: check if VanillaHopper has same blocktypeid
				if ($this->getHoppersPlaced() > $this->getCurrentLevel() * 20) {
					$event->cancel();
					$player->sendMessage(Skyblock::ERROR_PREFIX . "You must upgrade your island size in order to place more hoppers.");
				} else $this->setHoppersPlaced($this->getHoppersPlaced() + 1);
			}
		}
    }

	public function serialize() : string {
		return serialize([
			"name" => $this->getName(),
			"contributed" => $this->getAmountContributed(),
			"level" => $this->getCurrentLevel(),
			"hoppersPlaced" => $this->getHoppersPlaced(),
			"island" => $this->getCurrentLevel()
		]);
	}

	public function upgraded(): void
	{
		// TODO: Implement upgraded() method.
	}
}