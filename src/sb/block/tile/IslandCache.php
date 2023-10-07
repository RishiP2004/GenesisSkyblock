<?php

namespace sb\block\tile;

use pocketmine\block\tile\Chest;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;
use sb\entity\TextEntity;
use sb\utils\MathUtils;
use sb\world\islandCache\IslandCacheHandler;
use sb\world\islandCache\IslandCaches;

class IslandCache extends Chest {
	protected ?TextEntity $entity = null;

	public const TAG_TIMER = 'Timer';

	private ?int $timer = null;

	public function __construct(World $world, Vector3 $pos) {
		parent::__construct($world, $pos);

		$this->init();
		IslandCacheHandler::$islandCaches[] = $this;
	}

	public function init() : void {
		if(is_null($this->timer)) $this->timer = IslandCaches::DESPAWN_TIME * 30;

		$this->entity = new TextEntity(Location::fromObject($this->getBlock()->getPosition()->add(0.5, 1, 0.5), $this->getBlock()->getPosition()->getWorld()));
		$this->entity->setDespawnAfter(9999999999999);
		$this->entity->spawnToAll();
		$this->entity->setText("§b§lIsland Cache: \n\n§b§lExpires in " . MathUtils::getFormattedTime($this->getTimer()));
	}

	public function tick() : void {
		//to nearest half min.
		if(($this->getTimer() * 2) <= 0) {
			$this->getBlock()->getPosition()->getWorld()->setBlock($this->getBlock()->getPosition(), VanillaBlocks::AIR());
			$this->close();
		}
		$this->decreaseTimer();
	}

	public function close() : void {
		if($this->entity !== null){
			if(!$this->entity->isClosed()){
				if(!$this->entity->isFlaggedForDespawn()){
					$this->entity->flagForDespawn();
				}
			}
		}
		parent::close();
	}

	public function decreaseTimer() {
		$this->timer--;
	}

	public function getTimer() : int {
		return $this->timer;
	}

	public function addAdditionalSpawnData(CompoundTag $nbt) : void {
		parent::addAdditionalSpawnData($nbt);
		$nbt->setString(self::TAG_ID, "Chest");
		$nbt->setString(self::TAG_TIMER, ($this->timer === null ? 0 : $this->getTimer()));
	}

	protected function writeSaveData(CompoundTag $nbt) : void{
		parent::writeSaveData($nbt);
		$nbt->setLong(self::TAG_TIMER, $this->getTimer());
	}

	public function readSaveData(CompoundTag $nbt) : void {
		parent::readSaveData($nbt);
		$this->timer = ((int) $nbt->getLong(self::TAG_TIMER, 0));
	}
}