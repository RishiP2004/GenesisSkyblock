<?php

namespace sb\islands;

use czechpmdevs\multiworld\generator\void\VoidGenerator;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\TextFormat;
use sb\islands\utils\IslandGenerators;
use sb\lang\CustomKnownTranslationFactory;
use Symfony\Component\Filesystem\Path;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use sb\database\IslandDB;
use sb\player\CorePlayer;
use sb\scheduler\server\FileCopyAsyncTask;
use sb\Skyblock;
//todo: islandUpgrades should be handled better.
final class IslandManager {
    use SingletonTrait;

    public static Vector3 $defaultSpawn;

    /** @var array<string, Island> */
    public array $islands = [];

    public function __construct() {
		self::$instance = $this;

		IslandDB::get()->executeGeneric("islands.init");
        self::$defaultSpawn = new Vector3(0, 72, 0);
    }

	public function getIslands() : array {
		return $this->islands;
	}

    public function retrieveWarpableIslands() : array {
		$avaliable = [];

		foreach ($this->islands as $island){

			if($island->isLocked()) continue;
			if(!$island->isWorldLoaded()) continue;

			$avaliable[] = $island;
		}

		return $avaliable;
    }

    public function createIsland(CorePlayer $player, string $name, string $type) : Island {
		$id = $this->makeId();

		IslandDB::get()->executeInsert("islands.register", [
			"id" => $id,
			"name" => $name,
			"leader" => $player->getName(),
			"type" => $type
		]);
		$island = new Island($id);
		$island->name = $name;
		$island->leader = $player->getName();
		$island->type = $type;
		$island->setupDefaults();
		$player->getCoreUser()->setIsland($id);
		$player->islandName = $name; //temp islandCache

		$this->generateIsland($player, $island);
		$player->sendMessage(CustomKnownTranslationFactory::island_created());
		return $this->islands[$id] = $island;
    }

	public final function generateIsland(CorePlayer $player, Island $island) {
		@mkdir($island->getWorldDir());

		Server::getInstance()->getAsyncPool()->submitTask(
			new FileCopyAsyncTask(
				Path::join(Skyblock::getInstance()->getDataFolder(), "islands", IslandGenerators::GENERATOR_WORLD[$island->getType()]),
				Path::join($island->getWorldDir()),
				function() use ($player, $island) {
					$island->teleport($player, true);
				}
			)
		);
	}
	public function getAllSortableData(callable $callback) : void {
		IslandDB::get()->executeSelect("islands.top", [], function(array $rows) use($callback) {
			$arr = [];

			foreach($rows as [
					"name" => $name,
					"value" => $value,
					"leader" => $leader,
					"members" => $members,
			]) {
				$arr = [
					$name => [$value, $leader, $members]
				];
			}
			$callback($arr);
		});
	}

	public function getTopIslands(int $pageSize, int $page, callable $callback) {
		$this->getAllSortableData(function($data) use($pageSize, $page, $callback) {
			uasort($data, function ($a, $b) {
				return $b[0] - $a[0];
			});
			$value = array_map(function ($data) { return $data[0]; }, $data);
			$value = array_chunk($value, $pageSize, true); //DEFAULT SIZE SHOWN IS 5
			$page = min(count($value), max(1, $page));

			$callback($value[$page - 1] ?? []);
		});
	}

	public static function sendTopIslandsMenu(CorePlayer $player) : void {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
		$menu->setListener(InvMenu::readonly(fn() => null));
		$topIslands = [];

		IslandDB::get()->executeSelect("islands.top", [], function(array $rows) use(&$topIslands) {
			foreach($rows as ["name" => $name, "value" => $value, "leader" => $leader, "members" => $members]) {
				$topIslands[$name] = [$value, $leader, $members];
			}
		});
		$map = [
			10 => 1,
			12 => 2,
			14 => 3,
			16 => 4,
			18 => 5,
			20 => 6,
			22 => 7,
			24 => 8,
			26 => 9,
			28 => 10,
			30 => 11,
			32 => 12,
			34 => 13,
			36 => 14,
			38 => 15,
			40 => 16
		]; //wtf LOL

		$item = VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::PLAYER())->asItem();
		$index = 0;
		foreach($topIslands as $name => [$value, $leader, $members]) {
			if($index > 15) break;
			$menu->getInventory()->setItem($map[$index] + 1, $item->setCustomName(TextFormat::GOLD . $name . " \n\nPlace: " . $map[$index] . "\n" . TextFormat::GRAY . "Value: " . TextFormat::GREEN  . "$" . $value));
			$index++;
		}

		$menu->send($player);
	}

    public function makeId() : string {
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
		$charactersLength = strlen($characters);

		$makeId = function () use ($characters, $charactersLength) : string {
			$id = "";
			for ($i = 0; $i < 16; $i++) {
				$id .= $characters[rand(0, $charactersLength - 1)];
			}
			return $id;
		};
		$newId = $makeId();
		while (isset($this->islands[$newId])) {
			$newId = $makeId();
		}
		return $newId;
    }

    public function getIslandFromName(string $name) : ?Island {
		$found = null;
		foreach($this->islands as $i => $island) {
			if($island->getName() === $name) {
				$found = $island;
				break;
			}
		}
		return $found;
    }

	public function isIslandWorld(World|string $world): bool {
		return str_contains(($world instanceof World ? $world->getFolderName() : $world), "is-");
	}
}