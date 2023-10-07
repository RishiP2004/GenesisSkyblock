<?php

namespace sb\block\tile;

use pocketmine\block\tile\Tile;
use pocketmine\nbt\NbtDataException;
use pocketmine\nbt\tag\CompoundTag;

class Crop extends Tile {
	public function readSaveData(CompoundTag $nbt) : void {}

	protected function writeSaveData(CompoundTag $nbt) : void {}
}