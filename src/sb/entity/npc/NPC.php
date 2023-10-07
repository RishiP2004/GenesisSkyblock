<?php

declare(strict_types = 1);

namespace sb\entity\npc;

use pocketmine\item\VanillaItems;

use pocketmine\network\mcpe\convert\{
	LegacySkinAdapter,
	TypeConverter
};

use pocketmine\network\mcpe\protocol\types\GameMode;

use pocketmine\entity\{
	EntitySizeInfo,
	Location,
	Skin,
	Entity
};
use pocketmine\network\mcpe\protocol\{
	AddPlayerPacket,
	PlayerListPacket,
	MovePlayerPacket,
	SetActorDataPacket,
	UpdateAbilitiesPacket};
use pocketmine\network\mcpe\protocol\types\{
	AbilitiesData,
	AbilitiesLayer,
	command\CommandPermissions,
	DeviceOS,
	entity\EntityMetadataCollection,
	entity\EntityMetadataFlags,
	entity\EntityMetadataProperties,
	entity\MetadataProperty,
	entity\StringMetadataProperty,
	PlayerListEntry,
	PlayerPermissions};

use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;

use Ramsey\Uuid\Uuid;
use sb\player\CorePlayer;

abstract class NPC {
	private array $spawnedTo = [];

	private int $entityId;
	private $uuid;

	private EntityMetadataCollection $networkProperties;

	const MAX_NPC_DISTANCE = 16;

	public function __construct(
		private readonly string   $name,
		private readonly Location $location,
		private readonly string   $nameTag,
		private readonly float    $scale,
	) {
		$this->entityId = Entity::nextRuntimeId();
		$this->uuid = Uuid::uuid4();
		$this->networkProperties = new EntityMetadataCollection();
	}

	public final function getName() : string {
		return $this->name;
	}

	public function getLocation() : Location {
		return $this->location;
	}

	public function getNameTag() : string {
		return $this->nameTag;
	}

	public abstract function getSkin() : Skin;

	public abstract function getSize() : EntitySizeInfo;

	public function getScale() : float {
		return $this->scale;
	}

	public abstract function onInteract(CorePlayer $player) : void;

	public function getUuid() {
		return $this->uuid;
	}

	public function getEntityId() : int {
		return $this->entityId;
	}

	public function sendData(CorePlayer $player, ?array $data = null) : void{
		if(!is_array($player)) {
			$player = [$player];
		}
		$pk = SetActorDataPacket::create(
			$this->getEntityId(),
			$data ?? $this->getSyncedNetworkData(false),
			new PropertySyncData([], []),
			0
		);
		foreach($player as $p){
			$p->getNetworkSession()->sendDataPacket(clone $pk);
		}
	}

	public function isSpawnedTo(CorePlayer $player) : bool {
		return isset($this->spawnedTo[$player->getName()]);
	}

	public function spawnTo(CorePlayer $player) : void {
		$skin = new LegacySkinAdapter();
		$player->getNetworkSession()->sendDataPacket(PlayerListPacket::add([PlayerListEntry::createAdditionEntry($this->getUuid(), $this->getEntityId(), $this->getName(), $skin->toSkinData($this->getSkin()))]));

		$nameTag = $this->getNameTag();

		$player->getNetworkSession()->sendDataPacket(AddPlayerPacket::create(
			$this->getUuid(),
			$nameTag,
			$this->getEntityId(),
			"",
			$this->getLocation()->asVector3(),
			null,
			$this->getLocation()->pitch,
			$this->getLocation()->yaw,
			$this->getLocation()->yaw,
			ItemStackWrapper::legacy(TypeConverter::getInstance()->coreItemStackToNet(VanillaItems::AIR())),
			GameMode::SURVIVAL,
			[],
			new PropertySyncData([], []),
			UpdateAbilitiesPacket::create(new AbilitiesData(CommandPermissions::NORMAL, PlayerPermissions::VISITOR, $this->getEntityId(), [
				new AbilitiesLayer(
					AbilitiesLayer::LAYER_BASE,
					array_fill(0, AbilitiesLayer::NUMBER_OF_ABILITIES, false),
					0.0,
					0.0
				)
			])),
			[],
			"",
			DeviceOS::UNKNOWN
		));

		$this->sendData($player, [EntityMetadataProperties::NAMETAG => new StringMetadataProperty($nameTag)]);
		$player->getNetworkSession()->sendDataPacket(PlayerListPacket::remove([PlayerListEntry::createRemovalEntry($this->uuid)]));
		$this->spawnedTo[$player->getName()] = true;
	}

	public function rotateTo(CorePlayer $player) : void {
		$NPCPos = $this->getLocation()->asVector3();

		if($this->isSpawnedTo($player) && $player->getPosition()->distance($NPCPos) <= self::MAX_NPC_DISTANCE) {
			$x = $NPCPos->x - $player->getPosition()->getX();
			$y = $NPCPos->y - $player->getPosition()->getY();
			$z = $NPCPos->z - $player->getPosition()->getZ();

			if (sqrt($x * $x + $z * $z) == 0) return;
			if (sqrt($x * $x + $z * $z + $y * $y) == 0) return;

			$yaw = asin($x / sqrt($x * $x + $z * $z)) / 3.14 * 180;
			$pitch = round(asin($y / sqrt($x * $x + $z * $z + $y * $y)) / 3.14 * 180);

			if($z > 0) {
				$yaw = -$yaw + 180;
			}
			$packet = MovePlayerPacket::create($this->getEntityId(), $this->getLocation()->add(0, 1.62, 0), $pitch, $yaw, $yaw, MovePlayerPacket::MODE_NORMAL, true, 0, 0, 0, 0);

			$player->getNetworkSession()->sendDataPacket($packet);
		}
	}

	/**
	 * @return MetadataProperty[]
	 */
	final public function getSyncedNetworkData(bool $dirtyOnly) : array {
		$this->syncNetworkData();
		return $dirtyOnly ? $this->networkProperties->getDirty() : $this->networkProperties->getAll();
	}

	public function syncNetworkData() : void {
		$this->networkProperties->setByte(EntityMetadataProperties::ALWAYS_SHOW_NAMETAG, 1);
		$this->networkProperties->setFloat(EntityMetadataProperties::BOUNDING_BOX_HEIGHT, $this->getSize()->getHeight() / $this->getScale());
		$this->networkProperties->setFloat(EntityMetadataProperties::BOUNDING_BOX_WIDTH, $this->getSize()->getWidth() / $this->getScale());
		$this->networkProperties->setFloat(EntityMetadataProperties::SCALE, $this->getScale());
		$this->networkProperties->setLong(EntityMetadataProperties::LEAD_HOLDER_EID, -1);
		$this->networkProperties->setLong(EntityMetadataProperties::OWNER_EID, $this->ownerId ?? -1);
		$this->networkProperties->setLong(EntityMetadataProperties::TARGET_EID, $this->targetId ?? 0);
		$this->networkProperties->setString(EntityMetadataProperties::NAMETAG, $this->getNameTag());

		$this->networkProperties->setGenericFlag(EntityMetadataFlags::AFFECTED_BY_GRAVITY, false);
		$this->networkProperties->setGenericFlag(EntityMetadataFlags::CAN_SHOW_NAMETAG, true);
		$this->networkProperties->setGenericFlag(EntityMetadataFlags::HAS_COLLISION, true);
		$this->networkProperties->setGenericFlag(EntityMetadataFlags::IMMOBILE, false);
		$this->networkProperties->setGenericFlag(EntityMetadataFlags::INVISIBLE, false);
		$this->networkProperties->setGenericFlag(EntityMetadataFlags::ONFIRE, false);
		$this->networkProperties->setGenericFlag(EntityMetadataFlags::SNEAKING, false);
		$this->networkProperties->setGenericFlag(EntityMetadataFlags::WALLCLIMBING, false);
	}

	public function despawnFrom(CorePlayer $player) : void {
		unset($this->spawnedTo[$player->getName()]);

		$skin = new LegacySkinAdapter();
		$player->getNetworkSession()->sendDataPacket(PlayerListPacket::remove([PlayerListEntry::createAdditionEntry($this->getUuid(), $this->getEntityId(), $this->getName(), $skin->toSkinData($this->getSkin()))]));
	}
}
