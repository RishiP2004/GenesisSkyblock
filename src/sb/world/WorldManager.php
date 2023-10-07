<?php

declare(strict_types = 1);

namespace sb\world;

use pocketmine\utils\SingletonTrait;
use pocketmine\world\particle\FloatingTextParticle;
use sb\entity\EntityManager;
use sb\world\crates\CratesHandler;
use sb\world\islandCache\IslandCacheHandler;
use sb\world\koth\KothHandler;
use sb\entity\npc\NPC;
use sb\world\area\Area;

use sb\player\CorePlayer;

use pocketmine\Server;

use pocketmine\world\Position;
use sb\world\particle\CustomFloatingTextParticle;
use sb\world\prosperity\ProsperityHandler;
//todo: move islandCache and koth into server path.
final class WorldManager {
	use SingletonTrait;
	/**
	 * @var NPC[]
	 */
	private array $NPCs = [];
	/**
	 * @var FloatingTextParticle[]
	 */
	private array $holograms = [];

	private array $areas = [];

    public function __construct() {
		self::$instance = $this;

		Server::getInstance()->getWorldManager()->loadWorld("world");

		foreach(WorldData::HOLOGRAMS as $name => $data) {
			$posEx = explode(", ", $data["pos"]);
			$pos = new Position((float) $posEx[0], (float)$posEx[1], (float)$posEx[2], Server::getInstance()->getWorldManager()->getWorldByName($posEx[3]));

			$this->initHologram(new CustomFloatingTextParticle(
				$name,
				$pos,
				$data["text"],
			));
		}
		new CratesHandler();
		new KothHandler();
		new ProsperityHandler();
		new IslandCacheHandler();
		new KothHandler();
	}

	public function tick() : void {
		IslandCacheHandler::tick();
		KothHandler::tick();
	}

	public function spawnNPCs(CorePlayer $player) : void {
		foreach(EntityManager::getInstance()->getAllNPCs() as $NPC) {
			if($NPC instanceof NPC) $NPC->spawnTo($player);
		}
	}

	public function despawnNPCs(CorePlayer $player) : void {
		foreach(EntityManager::getInstance()->getAllNPCs() as $NPC) {
			if($NPC instanceof NPC) $NPC->despawnFrom($player);
		}
	}

	public function initHologram(CustomFloatingTextParticle $hologram) {
		$this->holograms[strtolower($hologram->getIdentifier())] = $hologram;
	}
	/**
	 * @return CustomFloatingTextParticle[]
	 */
	public function getHolograms() : array {
		return $this->holograms;
	}

	public function getHologram(string $hologram) : ?CustomFloatingTextParticle {
		$lowerKeys = array_change_key_case($this->holograms, CASE_LOWER);

		if(isset($lowerKeys[strtolower($hologram)])) {
			return $lowerKeys[strtolower($hologram)];
		}
		return null;
	}

	public function spawnHolograms(CorePlayer $player) : void {
		foreach($this->getHolograms() as $hologram) {
			if($hologram instanceof CustomFloatingTextParticle) {
				$hologram->updateFor($player);
			}
		}
	}
}