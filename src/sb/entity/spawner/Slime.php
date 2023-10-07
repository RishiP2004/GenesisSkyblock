<?php

declare(strict_types=1);

namespace sb\entity\spawner;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use sb\entity\SpawnerEntity;

class Slime extends SpawnerEntity {
	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(0.52, 0.52);
	}

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->setScale($this->getScale());
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::SLIME;
	}


	public function getName() : string{
		return "Slime";
	}

	public function getDrops() : array{
		return [
			VanillaItems::SLIMEBALL()->setCount(mt_rand(1, 2)),
		];
	}

	public function getXpDropAmount() : int{
		return 7;
	}
}