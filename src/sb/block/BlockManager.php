<?php

namespace sb\block;

use pocketmine\block\Beetroot;
use pocketmine\block\Block;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;
use pocketmine\block\BlockTypeTags;
use pocketmine\block\Carrot;
use pocketmine\block\Melon;
use pocketmine\block\MelonStem;
use pocketmine\block\NetherWartPlant;
use pocketmine\block\Potato;
use pocketmine\block\PumpkinStem;
use pocketmine\block\tile\TileFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\Wheat;
use pocketmine\data\bedrock\block\BlockStateNames;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\data\bedrock\block\convert\BlockStateDeserializerHelper;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateSerializerHelper;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\math\Facing;
use sb\block\tile\ChunkCollector;
use sb\block\tile\Crate;
use sb\block\tile\Crop;
use sb\block\tile\IslandCache;
use sb\block\tile\MonsterSpawner;

class BlockManager {
	public function __construct() {
		BlockOverrider::override("wheat",
			$block = new class(
				new BlockIdentifier(VanillaBlocks::WHEAT()->getTypeId()),
				VanillaBlocks::WHEAT()->getName(),
				new BlockTypeInfo(VanillaBlocks::WHEAT()->getBreakInfo(), VanillaBlocks::WHEAT()->getTypeTags())
			) extends Wheat {
				public function onRandomTick() : void {
					if($this->age < self::MAX_AGE) {
						$block = clone $this;
						++$block->age;
						$ev = new BlockGrowEvent($this, $block);
						$ev->call();
						if(!$ev->isCancelled()){
							$this->position->getWorld()->setBlock($this->position, $ev->getNewState());
						}
					}
				}
			});
		BlockOverrider::setDeserializer(BlockTypeNames::WHEAT, $block, fn(BlockStateReader $in) => BlockStateDeserializerHelper::decodeCrops(VanillaBlocks::WHEAT(), $in));
		BlockOverrider::setSerializer(BlockTypeNames::WHEAT, $block, fn(Wheat $block) => BlockStateSerializerHelper::encodeCrops($block, new BlockStateWriter(BlockTypeNames::WHEAT)));

		BlockOverrider::override("carrots",
			$block = new class(
				new BlockIdentifier(VanillaBlocks::CARROTS()->getTypeId()),
				VanillaBlocks::CARROTS()->getName(),
				new BlockTypeInfo(VanillaBlocks::CARROTS()->getBreakInfo(), VanillaBlocks::CARROTS()->getTypeTags())
			) extends Carrot {
				public function onRandomTick() : void {
					if($this->age < self::MAX_AGE) {
						$block = clone $this;
						++$block->age;
						$ev = new BlockGrowEvent($this, $block);
						$ev->call();
						if(!$ev->isCancelled()) {
							$this->position->getWorld()->setBlock($this->position, $ev->getNewState());
						}
					}
				}
			});
		BlockOverrider::setDeserializer(BlockTypeNames::CARROTS, $block, fn(BlockStateReader $in) => BlockStateDeserializerHelper::decodeCrops(VanillaBlocks::CARROTS(), $in));
		BlockOverrider::setSerializer(BlockTypeNames::CARROTS, $block, fn(Carrot $block) => BlockStateSerializerHelper::encodeCrops($block, new BlockStateWriter(BlockTypeNames::CARROTS)));

		BlockOverrider::override("beetroots",
			$block = new class(
				new BlockIdentifier(VanillaBlocks::BEETROOTS()->getTypeId()),
				VanillaBlocks::BEETROOTS()->getName(),
				new BlockTypeInfo(VanillaBlocks::BEETROOTS()->getBreakInfo(), VanillaBlocks::BEETROOTS()->getTypeTags())
			) extends Beetroot {
				public function onRandomTick() : void {
					if($this->age < self::MAX_AGE) {
						$block = clone $this;
						++$block->age;
						$ev = new BlockGrowEvent($this, $block);
						$ev->call();
						if(!$ev->isCancelled()) {
							$this->position->getWorld()->setBlock($this->position, $ev->getNewState());
						}
					}
				}
			});
		BlockOverrider::setDeserializer("beetroots", $block, fn(BlockStateReader $in) => BlockStateDeserializerHelper::decodeCrops(VanillaBlocks::BEETROOTS(), $in));
		BlockOverrider::setSerializer("beetroots", $block, fn(Beetroot $block) => BlockStateSerializerHelper::encodeCrops($block, new BlockStateWriter(BlockTypeNames::BEETROOT)));

		BlockOverrider::override("potatoes",
			$block = new class(
				new BlockIdentifier(VanillaBlocks::POTATOES()->getTypeId()),
				VanillaBlocks::POTATOES()->getName(),
				new BlockTypeInfo(VanillaBlocks::POTATOES()->getBreakInfo(), VanillaBlocks::POTATOES()->getTypeTags())
			) extends Potato {
				public function onRandomTick() : void {
					if($this->age < self::MAX_AGE) {
						$block = clone $this;
						++$block->age;
						$ev = new BlockGrowEvent($this, $block);
						$ev->call();
						if(!$ev->isCancelled()) {
							$this->position->getWorld()->setBlock($this->position, $ev->getNewState());
						}
					}
				}
			});
		BlockOverrider::setDeserializer(BlockTypeNames::POTATOES, $block, fn(BlockStateReader $in) => BlockStateDeserializerHelper::decodeCrops(VanillaBlocks::POTATOES(), $in));
		BlockOverrider::setSerializer(BlockTypeNames::POTATOES, $block, fn(Potato $block) => BlockStateSerializerHelper::encodeCrops($block, new BlockStateWriter(BlockTypeNames::POTATOES)));

		BlockOverrider::override("nether_wart",
			$block = new class(
				new BlockIdentifier(VanillaBlocks::NETHER_WART()->getTypeId()),
				VanillaBlocks::NETHER_WART()->getName(),
				new BlockTypeInfo(VanillaBlocks::NETHER_WART()->getBreakInfo(), VanillaBlocks::NETHER_WART()->getTypeTags())
			) extends NetherWartPlant {
				public function onRandomTick() : void {
					if($this->age < self::MAX_AGE && mt_rand(0, 3) === 0){ //Still growing
						$block = clone $this;
						$block->age++;
						$ev = new BlockGrowEvent($this, $block);
						$ev->call();
						if(!$ev->isCancelled()){
							$this->position->getWorld()->setBlock($this->position, $ev->getNewState());
						}
					}
				}
			});
		BlockOverrider::setDeserializer(BlockTypeNames::NETHER_WART, $block, function(BlockStateReader $in) : Block{return VanillaBlocks::NETHER_WART()->setAge($in->readBoundedInt(BlockStateNames::AGE, 0, 3));});
		BlockOverrider::setSerializer(BlockTypeNames::NETHER_WART, $block, function(NetherWartPlant $block) : BlockStateWriter{return BlockStateWriter::create(BlockTypeNames::NETHER_WART)->writeInt(BlockStateNames::AGE, $block->getAge());});

		BlockOverrider::override("pumpkin",
			$block = new class(
				new BlockIdentifier(VanillaBlocks::PUMPKIN()->getTypeId(), Crop::class),
				VanillaBlocks::PUMPKIN()->getName(),
				new BlockTypeInfo(VanillaBlocks::PUMPKIN()->getBreakInfo(), VanillaBlocks::PUMPKIN()->getTypeTags())
			) extends Melon {});
		BlockOverrider::setDeserializer(BlockTypeNames::PUMPKIN, $block, function(BlockStateReader $in) use($block) : Block{
			$in->ignored(BlockStateNames::MC_CARDINAL_DIRECTION); //obsolete
			return $block;
		});
		BlockOverrider::setSerializer(BlockTypeNames::PUMPKIN, $block, function() : BlockStateWriter {
			return BlockStateWriter::create(BlockTypeNames::PUMPKIN)
				->writeCardinalHorizontalFacing(Facing::SOUTH); //no longer used
		});

		BlockOverrider::override("pumpkin_stem",
			$block = new class(
				new BlockIdentifier(VanillaBlocks::PUMPKIN_STEM()->getTypeId()),
				VanillaBlocks::PUMPKIN_STEM()->getName(),
				new BlockTypeInfo(VanillaBlocks::PUMPKIN_STEM()->getBreakInfo(), VanillaBlocks::PUMPKIN_STEM()->getTypeTags())
			) extends PumpkinStem {
				public function onRandomTick() : void {
					if($this->getFacing() === Facing::UP){
						$world = $this->position->getWorld();
						if($this->age < self::MAX_AGE){
							$block = clone $this;
							++$block->age;
							$ev = new BlockGrowEvent($this, $block);
							$ev->call();
							if(!$ev->isCancelled()){
								$world->setBlock($this->position, $ev->getNewState());
							}
						}else{
							$grow = $this->getPlant();
							foreach(Facing::HORIZONTAL as $side){
								if($this->getSide($side)->hasSameTypeId($grow)){
									return;
								}
							}

							$facing = Facing::HORIZONTAL[array_rand(Facing::HORIZONTAL)];
							$side = $this->getSide($facing);
							if($side->getTypeId() === BlockTypeIds::AIR && $side->getSide(Facing::DOWN)->hasTypeTag(BlockTypeTags::DIRT)){
								$ev = new BlockGrowEvent($side, $grow);
								$ev->call();
								if(!$ev->isCancelled()){
									$world->setBlock($this->position, $this->setFacing($facing));
									$world->setBlock($side->position, $ev->getNewState());
								}
							}
						}
					}
				}
			});
		BlockOverrider::setDeserializer(BlockTypeNames::PUMPKIN_STEM, $block, fn(BlockStateReader $in) => BlockStateDeserializerHelper::decodeStem(VanillaBlocks::PUMPKIN_STEM(), $in));
		BlockOverrider::setSerializer(BlockTypeNames::PUMPKIN_STEM, $block, fn(PumpkinStem $block) => BlockStateSerializerHelper::encodeStem($block, new BlockStateWriter(BlockTypeNames::PUMPKIN_STEM)));

		BlockOverrider::override("melon",
			$block = new class(
				new BlockIdentifier(VanillaBlocks::MELON()->getTypeId(), Crop::class),
				VanillaBlocks::MELON()->getName(),
				new BlockTypeInfo(VanillaBlocks::MELON_STEM()->getBreakInfo(), VanillaBlocks::MELON()->getTypeTags())
			) extends Melon {});
		BlockOverrider::setDeserializer(BlockTypeNames::MELON_BLOCK, $block, fn() => BlockTypeNames::MELON_BLOCK);
		BlockOverrider::setSerializer(BlockTypeNames::MELON_BLOCK, $block, fn() => BlockStateWriter::create(BlockTypeNames::MELON_BLOCK));

		BlockOverrider::override("melon_stem",
			$block = new class(
				new BlockIdentifier(VanillaBlocks::MELON_STEM()->getTypeId()),
				VanillaBlocks::MELON_STEM()->getName(),
				new BlockTypeInfo(VanillaBlocks::MELON_STEM()->getBreakInfo(), VanillaBlocks::MELON_STEM()->getTypeTags())
			) extends MelonStem {
				public function onRandomTick() : void {
					if($this->getFacing() === Facing::UP){
						$world = $this->position->getWorld();

						if($this->age < self::MAX_AGE){
							$block = clone $this;
							++$block->age;
							$ev = new BlockGrowEvent($this, $block);
							$ev->call();
							if(!$ev->isCancelled()){
								$world->setBlock($this->position, $ev->getNewState());
							}
						}else{
							$grow = $this->getPlant();
							foreach(Facing::HORIZONTAL as $side){
								if($this->getSide($side)->hasSameTypeId($grow)){
									return;
								}
							}

							$facing = Facing::HORIZONTAL[array_rand(Facing::HORIZONTAL)];
							$side = $this->getSide($facing);
							if($side->getTypeId() === BlockTypeIds::AIR && $side->getSide(Facing::DOWN)->hasTypeTag(BlockTypeTags::DIRT)){
								$ev = new BlockGrowEvent($side, $grow);
								$ev->call();
								if(!$ev->isCancelled()){
									$world->setBlock($this->position, $this->setFacing($facing));
									$world->setBlock($side->position, $ev->getNewState());
								}
							}
						}
					}
				}
			});
		BlockOverrider::setDeserializer(BlockTypeNames::MELON_STEM, $block, fn(BlockStateReader $in) => BlockStateDeserializerHelper::decodeStem(VanillaBlocks::MELON_STEM(), $in));
		BlockOverrider::setSerializer(BlockTypeNames::MELON_STEM, $block, fn(MelonStem $block) => BlockStateSerializerHelper::encodeStem($block, new BlockStateWriter(BlockTypeNames::MELON_STEM)));

		TileFactory::getInstance()->register(Crate::class);
		TileFactory::getInstance()->register(IslandCache::class);
		TileFactory::getInstance()->register(ChunkCollector::class);
		TileFactory::getInstance()->register(Crop::class, ["minecraft:melon", "minecraft:pumpkin"]);
		TileFactory::getInstance()->register(MonsterSpawner::class, ['MobSpawner', 'minecraft:mob_spawner']);
	}
}