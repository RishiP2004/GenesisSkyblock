<?php

namespace sb\world\islandCache;

use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use sb\block\tile\IslandCache;
use sb\item\CustomItemParser;
use sb\player\CorePlayer;
use sb\utils\Reward;

class IslandCacheHandler implements IslandCaches {
	public static int $runs = 0;
	/**
	 * @var IslandCache[]
	 */
	public static array $islandCaches = [];
	/**
	 * @var Reward[]
	 */
	public static array $items = [];

	public function __construct() {
		foreach(self::ITEMS as $item) {
			$items = [];
			$parsed = CustomItemParser::getInstance()->parse($item);

			if(!is_null($parsed)) {
				$reward = new Reward(($parsed), function(CorePlayer $player) use($parsed) {
					$player->getInventory()->addItem($parsed);
				}, 5); //todo: custom chances
				$items[] = $reward;
			}

			self::$items = $items;
		}
	}

	public static function getItems() : array {
		return self::$items;
	}

	public static function getItem(int $loop = 0) : Reward {
		$chance = mt_rand(0, 100);
		$reward = self::$items[array_rand(self::$items)];

		if($loop >= 10) return $reward;
		if($reward->getChance() <= $chance) return self::getItem($loop + 1);
		return $reward;
	}

	public static function getAll() : array {
		return self::$islandCaches;
	}

	public static function tick() : void {
		self::$runs++; //this is bad btw constantly incrementing properties you should use a time stamp

		if(self::$runs === 60) {//self::SPAWN_TIME * 60) {
			self::spawn();
			self::$runs = 0;
		}
		foreach (self::getAll() as $islandCache) {
			$islandCache->tick();
		}
	}

	public static function spawn() : void {
		$coords = self::getRandomCoordinates();
		$world = Server::getInstance()->getWorldManager()->getWorldByName("world");

		$x = $coords->getX();
		$z = $coords->getY(); //weird ik

		$world->orderChunkPopulation($x >> 4, $z >> 4, null)->onCompletion(function() use($x, $z, $world) : void {
			$y = $world->getHighestBlockAt($x, $z) + 1;
			$coords = new Vector3((int) $x, (int) $y, (int) $z);
			$block = VanillaBlocks::CHEST();

			$world->setBlock($coords, $block);

			$tile = $world->getTile($coords);
			$tile->close();

			$newTile = new IslandCache($world, $coords);
			$world->addTile($newTile);

			$x = $coords->getX();
			$y = $coords->getY();
			$z = $coords->getZ();

			self::$runs = 0;

			Server::getInstance()->broadcastMessage(TextFormat::colorize("&r&d&l(!) &r&dAn island cache has been spawned at &l{$x}x&r&d, &r&d&l{$z}z"));
		}, function() : void {});
	}

	public static function getRandomCoordinates(): Vector2 {
		$minX = self::AREA["minX"];
		$maxX = self::AREA["maxX"];
		$minZ = self::AREA["minZ"];
		$maxZ = self::AREA["maxZ"];
		$x = mt_rand($minX, $maxX);
		$z = mt_rand($minZ, $maxZ);
		return new Vector2($x, $z);
	}
}