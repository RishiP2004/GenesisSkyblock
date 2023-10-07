<?php

namespace sb\block\tile;

use muqsit\invmenu\InvMenu;
use pocketmine\block\tile\Chest;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\World;
use sb\entity\TextEntity;
use sb\player\CorePlayer;
use sb\world\crates\CratesHandler;
use pocketmine\nbt\tag\CompoundTag;

class Crate extends Chest {
	public const TAG_TYPE = "Type";

	protected ?TextEntity $entity = null;

	private ?\sb\world\crates\Crate $type = null;

	private $initialized = false;

	public function __construct(World $world, Vector3 $pos, $initial = false) {
		parent::__construct($world, $pos);

		if(!$initial) {
			$this->init();
		}
	}

	public function getType(): ?\sb\world\crates\Crate {
		return $this->type;
	}

	public function setType(\sb\world\crates\Crate $crate) : void {
		$this->type = $crate;
	}

	public function isInitialized() : bool {
		return $this->initialized;
	}
	//do better method in future like Await.
	public function init() {
		if($this->getType() == null) { //always will happen?
			$this->initialized = false;
			return;
		}
		$this->entity = new TextEntity(Location::fromObject($this->getPosition()->add(0.5, 1, 0.5), $this->getPosition()->getWorld()));
		$this->entity->setDespawnAfter(9999999999999);
		$this->entity->spawnToAll();
		$this->entity->setText($this->getType()->getColouredName() . TextFormat::RESET . " \n" . TextFormat::GRAY . "Left-Click to preview\n" . TextFormat::GRAY . "Right-Click to open" . TextFormat::EOL);
		$this->initialized = true;
	}

	public function open(CorePlayer $player) : void {
		$this->broadcastParticle($player);
		$this->getType()->getReward()->getCallback()($player);
	}

	public function previewRewards(CorePlayer $player) : void {
		$menu = InvMenu::create(InvMenu::TYPE_CHEST);
		$menu->setListener(InvMenu::readonly());
		$menu->setName($this->getType()->getColouredName() . TextFormat::GRAY . " Crate");

		foreach($this->getType()->getRewards() as $name => $reward) {
			$item = $reward->getItem();
			$item->setLore(["§r§7Chance: " . $reward->getChance()]);
			$menu->getInventory()->addItem($item);
		}
		$menu->send($player);
	}

	public function broadcastParticle(CorePlayer $player) : void {
		$particle = new BlockBreakParticle(VanillaBlocks::DIAMOND());
		$this->getPosition()->getWorld()->addParticle($this->getPosition()->add(0.5, 0.5, 0.5), $particle, [$player]);
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

	public function addAdditionalSpawnData(CompoundTag $nbt) : void {
		parent::addAdditionalSpawnData($nbt);
		$nbt->setString(self::TAG_ID, "Chest");
		$nbt->setString(self::TAG_TYPE, ($this->type === null ? "Common" : $this->type->getName()) . " Crate");
	}

	public function readSaveData(CompoundTag $nbt) : void {
		parent::readSaveData($nbt);
		$name = $nbt->getString(self::TAG_TYPE);

		if(is_null(CratesHandler::get($name))) {
			$this->initialized = false;
		} else $this->setType(CratesHandler::get($name)); //wont ever happen due to loading order.
	}

	protected function writeSaveData(CompoundTag $nbt): void {
		parent::writeSaveData($nbt);
		$nbt->setString(self::TAG_TYPE, $this->type->getName());
	}
}