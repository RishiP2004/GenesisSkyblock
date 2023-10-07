<?php

namespace sb\block\tile;

use pocketmine\block\tile\Chest;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\World;
use sb\entity\TextEntity;

class ChunkCollector extends Chest {
	public static array $chunkCache = [];
	private string $uniqueId;
	protected ?TextEntity $entity = null;

	public function __construct(World $world, Vector3 $pos) {
		$this->uniqueId = uniqid();

		parent::__construct($world, $pos);

		$this->init();
	}

	public static function getCache(string $pos) : array {
		return self::$chunkCache[strtolower($pos)] ?? [];
	}

	public static function addCache(string $pos, Chest $tile, $id) {
		self::$chunkCache[strtolower($pos)][$tile] = $id;
	}

	public static function removeCache(string $pos, $id) {
		unset(self::$chunkCache[strtolower($pos)][$id]);
	}

	protected function init() {
		self::addCache(self::getCacheIdentifier($this->getBlock()->getPosition()), $this, $this->uniqueId);

		$this->entity = new TextEntity(Location::fromObject($this->getPosition()->add(0.5, 1, 0.5), $this->getPosition()->getWorld()));
		$this->entity->setDespawnAfter(9999999999999);
		$this->entity->spawnToAll();
		$this->entity->setText("§a§lChunk Chest");
	}

	public function close() : void {
		self::removeCache($this->getPosition(), $this->uniqueId);

		if($this->entity !== null){
			if(!$this->entity->isClosed()){
				if(!$this->entity->isFlaggedForDespawn()){
					$this->entity->flagForDespawn();
				}
			}
		}
		parent::close();
	}

	public static function getCacheIdentifier(Position $pos): string {
		return $pos->getWorld()->getDisplayName() . ($pos->getFloorX() >> 4) . ($pos->getFloorZ() >> 4);
	}
}