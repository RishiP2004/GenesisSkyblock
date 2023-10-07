<?php

namespace sb\block;

use pocketmine\block\Block;
use pocketmine\data\bedrock\LegacyEntityIdToStringIdMap;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;
use sb\item\CustomItems;

class MonsterSpawner extends \pocketmine\block\MonsterSpawner {

	protected string $entityTypeId = ':';
	protected int $legacyEntityId = 0;

	public function getMaxStackSize() : Int{ return 64; }

	public function isAffectedBySilkTouch() : Bool{ return true; }

	public function place(BlockTransaction $tx, Item $item, Block $replace, Block $clicked, Int $face, Vector3 $click, ?Player $player = null) : Bool{
		$this->setLegacyEntityId($item->getNamedTag()->getInt('SpawnerEntityId', 0));
		return parent::place($tx, $item, $replace, $clicked, $face, $click, $player);
	}

	public function setLegacyEntityId(int $id) : self{
		$this->entityTypeId = LegacyEntityIdToStringIdMap::getInstance()->legacyToString($this->legacyEntityId = $id) ?? ':';
		return $this;
	}

	public function getLegacyEntityId() : Int{
		return $this->legacyEntityId;
	}

	public function onScheduledUpdate() : Void{
		$tile = $this->position->getWorld()->getTile($this->position);
		if(
			$tile instanceof \sb\block\tile\MonsterSpawner and
			$tile->onUpdate()
		) $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 1);
	}

	public function readStateFromWorld() : Block{
		parent::readStateFromWorld();

		$tile = $this->position->getWorld()->getTile($this->position);

		if(
			$tile instanceof \sb\block\tile\MonsterSpawner and
			$tile->getEntityTypeId() !== ':'
		){
			$this->entityTypeId = $tile->getEntityTypeId();
			$this->legacyEntityId = $tile->getLegacyEntityId();
		}

		return $this;
	}

	public function writeStateToWorld() : Void{
		parent::writeStateToWorld();

		$tile = $this->position->getWorld()->getTile($this->position);

		assert($tile instanceof \sb\block\tile\MonsterSpawner);

		if($tile->getEntityTypeId() == ':') $tile->setLegacyEntityId($this->legacyEntityId);
	}

	public function getSilkTouchDrops(Item $item) : array{
		$id = ($tile = $this->position->getWorld()->getTile($this->position)) instanceof \sb\block\tile\MonsterSpawner ? $tile->getLegacyEntityId() : $this->legacyEntityId;
		return [StringToItemParser::getInstance()->parse('52:'. $id) ?? CustomItems::MONSTER_SPAWNER()];
	}
}