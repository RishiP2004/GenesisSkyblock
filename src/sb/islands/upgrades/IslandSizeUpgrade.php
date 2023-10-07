<?php

namespace sb\islands\upgrades;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\math\Vector3;
use sb\islands\utils\IslandGenerators;
use sb\islands\traits\IslandCallTrait;
use sb\Skyblock;

class IslandSizeUpgrade extends IslandUpgrade {
	use IslandCallTrait;

    public function __construct(int $amountContributed, int $currentLevel, string $islandI) {
        parent::__construct(
            "Size",
            "Upgrade the size of your island.",
            [1000, 2000, 3000, 4000, 5000],
            $amountContributed,
            $currentLevel,
            $islandI
        );
    }

    public function interceptBreak(BlockBreakEvent $event) : void {}

    public function interceptPlace(BlockPlaceEvent $event) : void {
        $player = $event->getPlayer();
        $pos = $event->getBlockAgainst()->getPosition();

		$this->getIsland($this->getIslandId(), function($island) use ($player, $pos, $event) {
			if ((new Vector3($pos->getX(), 125, $pos->getZ()))->distance(IslandGenerators::getDefaultSpawn($island->getType())) > (20 * ($this->getCurrentLevel() + 1))){
				$event->cancel();
				$player->sendMessage(Skyblock::ERROR_PREFIX . "You must upgrade your island size in order to place blocks this far from spawn.");
			}
		});
    }

	public function serialize() : string {
		return serialize([
			"name" => $this->getName(),
			"contributed" => $this->getAmountContributed(),
			"level" => $this->getCurrentLevel(),
			"island" => $this->getCurrentLevel()
		]);
	}

	public function upgraded(): void
	{
		// TODO: Implement upgraded() method.
	}
}