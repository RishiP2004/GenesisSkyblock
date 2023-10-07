<?php

declare(strict_types=1);

namespace sb\entity;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
//used for island caches, chunk collector: (Multiple spawn objects w/o identifier)
class TextEntity extends Entity {
	private int $lived = 0;

	private string $text = "§cNot initialized yet";

	private int $despawnAfter = 100;

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);

		$this->setCanSaveWithChunk(false);
		$this->setNameTagAlwaysVisible(true);
		$this->setScale(0.001);
	}

	/**
	 * @param int $despawnAfter
	 */
	public function setDespawnAfter(int $despawnAfter) : void{
		$this->despawnAfter = $despawnAfter;
	}

	public function onUpdate(int $currentTick): bool
	{
		$this->lived++;
		if($this->lived >= $this->despawnAfter){
			$this->flagForDespawn();
		}
		return parent::onUpdate($currentTick);
	}

	/**
	 * @param string $text
	 */
	public function setText(string $text) : void{
		$this->text = $text;

		$this->setNameTag($text);
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(0, 0);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::EGG;
	}

	public function getNameTag() : string{
		return $this->text;
	}

	protected function getInitialDragMultiplier() : float {
		return 0.0;
	}

	protected function getInitialGravity() : float {
		return 0.0;
	}
}