<?php

namespace sb\entity;

use http\Exception\RuntimeException;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\data\bedrock\PotionTypeIds;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Location;
use pocketmine\entity\object\FallingBlock;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use sb\entity\npc\CoinFlipNPC;
use sb\entity\npc\IslandNPC;
use sb\entity\npc\KitNPC;
use sb\entity\npc\NPC;
use sb\entity\npc\ShopNPC;
use pocketmine\nbt\tag\CompoundTag;
use sb\entity\spawner\Blaze;
use sb\entity\spawner\Chicken;
use sb\entity\spawner\Cow;
use sb\entity\spawner\IronGolem;
use sb\entity\spawner\Mooshroom;
use sb\entity\spawner\Pig;
use sb\entity\spawner\Skeleton;
use sb\entity\spawner\Slime;
use sb\entity\spawner\Squid;
use sb\entity\spawner\Zombie;
use sb\entity\spawner\ZombiePigman;
//todo: clearlag?
class EntityManager {
	use SingletonTrait;
	/**
	 * @var NPC[]
	 */
	private array $npcs = [];

	private array $entities = [
		Blaze::class,
		Chicken::class,
		Cow::class,
		IronGolem::class,
		Mooshroom::class,
		Pig::class,
		Skeleton::class,
		Slime::class,
		Squid::class,
		Zombie::class,
		ZombiePigman::class
	];

	private array $entityIds = [];

	public function __construct() {
		self::$instance = $this;

		EntityFactory::getInstance()->register(
			CustomPotion::class,
			function (World $world, CompoundTag $nbt): CustomPotion {
				$potionType = PotionTypeIdMap::getInstance()->fromId(
					$nbt->getShort("PotionId", PotionTypeIds::WATER)
				);
				if ($potionType === null) {
					throw new SavedDataLoadingException();
				}
				return new CustomPotion(
					EntityDataHelper::parseLocation($nbt, $world),
					null,
					$potionType,
					$nbt
				);
			},
			["ThrownPotion", "minecraft:potion", "thrownpotion", "splashpotion"],
		);

		EntityFactory::getInstance()->register(
			FishingHook::class,
			function (World $world, CompoundTag $nbt): FishingHook {
				return new FishingHook(
					EntityDataHelper::parseLocation($nbt, $world),
					null
				);
			},
			["FishingHook", "minecraft:fishing_hook"]
		);
		EntityFactory::getInstance()->register(
			LightningBolt::class,
			function (World $world, CompoundTag $nbt) : LightningBolt {
				return new LightningBolt(
					EntityDataHelper::parseLocation($nbt, $world),
					15,
					$nbt
				);
			},
			["LightningBolt"]
		);
		EntityFactory::getInstance()->register(
			CustomFallingBlock::class,
			function (World $world, CompoundTag $nbt) : CustomFallingBlock {
				return new CustomFallingBlock(EntityDataHelper::parseLocation($nbt, $world), FallingBlock::parseBlockNBT(RuntimeBlockStateRegistry::getInstance(), $nbt), $nbt);
			},
			['FallingSand', 'minecraft:falling_block']
		);

		foreach($this->entities as $entity) {
			EntityFactory::getInstance()->register($entity, function(World $world, CompoundTag $nbt) use($entity): SpawnerEntity {
				return new $entity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
			}, [$entity::getNetworkTypeId()]);

			$this->entityIds[$entity::getNetworkTypeId()] = $entity;
		}
		$this->registerNPC("shop", new ShopNPC());
		$this->registerNPC("island", new IslandNPC());
		#$this->registerNPC("kit", new KitNPC());
		$this->registerNPC("coinflip", new CoinFlipNPC());
	}

	public function registerNPC(string $id, NPC $npc) : void {
		$this->npcs[$id] = $npc;
	}

	public function getNPC(string $id) : NPC {
		return $this->npcs[$id];
	}
	/**
	 * @return NPC[]
	 */
	public function getAllNPCs() : array {
		return $this->npcs;
	}
	//idk rn its late so ill hard write
	public function getEntityFor(string $entityTypeId, Location $location, $nbt) : SpawnerEntity {
		switch($entityTypeId) {
			case Blaze::getNetworkTypeId():
				return new Blaze($location, $nbt);
			case Chicken::getNetworkTypeId():
				return new Chicken($location, $nbt);
			case Cow::getNetworkTypeId():
				return new Cow($location, $nbt);
			case IronGolem::getNetworkTypeId():
				return new IronGolem($location, $nbt);
			case Mooshroom::getNetworkTypeId():
				return new Mooshroom($location, $nbt);
			case Pig::getNetworkTypeId():
				return new Pig($location, $nbt);
			case Skeleton::getNetworkTypeId():
				return new Skeleton($location, $nbt);
			case Slime::getNetworkTypeId():
				return new Slime($location, $nbt);
			case Squid::getNetworkTypeId():
				return new Squid($location, $nbt);
			case Zombie::getNetworkTypeId():
				return new Zombie($location, $nbt);
			case ZombiePigman::getNetworkTypeId():
				return new ZombiePigman($location, $nbt);
			default:
				throw new RuntimeException("smth fucked up");
		}
	}
}