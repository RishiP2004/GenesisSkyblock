<?php

declare(strict_types=1);

namespace sb\entity\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use sb\entity\SpawnerEntity;

class Skeleton extends SpawnerEntity {
	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(1.9, 0.6);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::SKELETON;
	}


	public function getName() : string{
		return "Skeleton";
	}

	public function getDrops() : array{
		return [
			VanillaItems::BONE()->setCount(mt_rand(1, 2)),
			VanillaItems::ARROW()->setCount(mt_rand(0, 1)),
		];
	}

	public function getXpDropAmount() : int{
		return 5;
	}
}