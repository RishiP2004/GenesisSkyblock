<?php

namespace sb\block\tile;

use pocketmine\block\tile\Spawnable;
use pocketmine\data\bedrock\LegacyEntityIdToStringIdMap;
use pocketmine\entity\Location;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskHandler;
use pocketmine\world\particle\MobSpawnParticle;
use pocketmine\world\World;
use sb\entity\EntityManager;
use sb\Skyblock;
use sb\entity\SpawnerEntity;

class MonsterSpawner extends Spawnable {
	protected const TAG_LEGACY_ENTITY_TYPE_ID = "EntityId";
	protected const TAG_ENTITY_TYPE_ID = "EntityIdentifier";

	protected const TAG_SPAWN_DELAY = "Delay";
	protected const TAG_MIN_SPAWN_DELAY = "MinSpawnDelay";
	protected const TAG_MAX_SPAWN_DELAY = "MaxSpawnDelay";

	protected const TAG_SPAWN_RANGE = "SpawnRange";
	protected const TAG_REQUIRED_PLAYER_RANGE = "RequiredPlayerRange";

	private int $spawnDelay = 0;
	private int $minSpawnDelay = 200;
	private int $maxSpawnDelay = 800;
	private int $spawnRange = 5;
	private int $requiredPlayerRange = 16;

	protected int $legacyEntityTypeId = 0;
	protected string $entityTypeId = ":";

	private ?TaskHandler $handler = null;

	public function __construct(World $world, Vector3 $pos){
		parent::__construct($world, $pos);

		$this->handler = Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(
			new ClosureTask(
				function() {
					if($this->canUpdate()) $this->onUpdate();
				}
			), 20
		);

	}

	public function canUpdate() : Bool{
		return (
			$this->entityTypeId !== ":" and
			$this->getPosition()->getWorld()->getNearestEntity($this->getPosition(), $this->requiredPlayerRange, Player::class) !== null
		);
	}

	public function getLegacyEntityId() : Int{
		return $this->legacyEntityTypeId;
	}

	public function setLegacyEntityId(Int $id) : Void{
		$this->entityTypeId = LegacyEntityIdToStringIdMap::getInstance()->legacyToString($this->legacyEntityTypeId = $id) ?? ':';
		if(($block = $this->getBlock()) instanceof \sb\block\MonsterSpawner) $block->setLegacyEntityId($id);
	}

	public function getEntityTypeId() : string{
		return $this->entityTypeId;
	}

	public function setEntityId(String $id) : void{
		$this->legacyEntityTypeId = array_search(
			$this->entityTypeId = $id, LegacyEntityIdToStringIdMap::getInstance()->getLegacyToStringMap()
		);
		if(($block = $this->getBlock()) instanceof \sb\block\MonsterSpawner) $block->setLegacyEntityId($this->legacyEntityTypeId);
	}

	public function readSaveData(CompoundTag $nbt) : void{
		$legacyIdTag = $nbt->getTag(self::TAG_LEGACY_ENTITY_TYPE_ID);
		if($legacyIdTag instanceof IntTag){
			$this->setLegacyEntityId($legacyIdTag->getValue());
		}else{
			$this->setEntityId($nbt->getString(self::TAG_ENTITY_TYPE_ID, ":"));
		}
		$this->spawnDelay = $nbt->getShort(self::TAG_SPAWN_DELAY, 200);
		$this->minSpawnDelay = $nbt->getShort(self::TAG_MIN_SPAWN_DELAY, 200);
		$this->maxSpawnDelay = $nbt->getShort(self::TAG_MAX_SPAWN_DELAY, 800);

		$this->requiredPlayerRange = $nbt->getShort(self::TAG_REQUIRED_PLAYER_RANGE, 16);
		$this->spawnRange = $nbt->getShort(self::TAG_SPAWN_RANGE, 5);
	}

	protected function writeSaveData(CompoundTag $nbt) : void {
		$nbt->setString(self::TAG_ENTITY_TYPE_ID, $this->entityTypeId);
		$nbt->setShort(self::TAG_SPAWN_DELAY, $this->spawnDelay);
		$nbt->setShort(self::TAG_MIN_SPAWN_DELAY, $this->minSpawnDelay);
		$nbt->setShort(self::TAG_MAX_SPAWN_DELAY, $this->maxSpawnDelay);
		$nbt->setShort(self::TAG_SPAWN_RANGE, $this->spawnRange);
		$nbt->setShort(self::TAG_REQUIRED_PLAYER_RANGE, $this->requiredPlayerRange);
	}

	protected function addAdditionalSpawnData(CompoundTag $nbt) : void{
		$nbt->setString(self::TAG_ENTITY_TYPE_ID, $this->entityTypeId);
	}

	public function onUpdate(): void {
		if($this->closed){
			$this->handler->cancel();
			return;
		}
		$blockPos = $this->getPosition();
			
		if($this->canUpdate()) {
			if (--$this->spawnDelay <= 0) {
				$this->spawnDelay = mt_rand($this->minSpawnDelay, $this->maxSpawnDelay);
				$range = $this->spawnRange;
				$spawnPos = $blockPos->getWorld()->getSafeSpawn($blockPos->add(mt_rand(-$range, $range), 0, mt_rand(-$range, $range)));
				$nearest = $this->findNearestEntity($this->getEntityTypeId());

				if ($nearest !== null) {
					$nearest->setStack($nearest->getStack() + 1);
					return;
				}
				$nbt = (new CompoundTag())->setInt("stack", 1);

				(EntityManager::getInstance()->getEntityFor($this->getEntityTypeId(), Location::fromObject($spawnPos, $blockPos->world), $nbt))->spawnToAll();
				$blockPos->getWorld()->addParticle($blockPos, new MobSpawnParticle(2, 2));
			}
		}
	}

	private function findNearestEntity(string $type) : ?SpawnerEntity {
		$pos = $this->getBlock()->getPosition();
		foreach ($this->getBlock()->getPosition()->getWorld()->getNearbyEntities(new AxisAlignedBB(
			$pos->x - 25,
			$pos->y - 25,
			$pos->z - 25,
			$pos->x + 25,
			$pos->y + 25,
			$pos->z + 25
		)) as $entity) {
			if ($entity->isAlive() and ($entity instanceof SpawnerEntity)) {
				if ($entity::getNetworkTypeId() === $type) {
					return $entity;
				}
			}
		}
		return null;
	}
}