<?php

declare(strict_types=1);

namespace sb\entity\spawner;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use sb\entity\SpawnerEntity;

class Mooshroom extends SpawnerEntity {
	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(1.3, 0.9);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::MOOSHROOM;
	}


	public function getName() : string{
		return "Mooshroom";
	}

	public function getDrops() : array{
		return [
			VanillaBlocks::BROWN_MUSHROOM()->asItem()->setCount(mt_rand(1, 2)),
			VanillaBlocks::RED_MUSHROOM()->asItem()->setCount(mt_rand(1, 2)),
		];
	}

	public function getXpDropAmount() : int{
		return 7;
	}
}