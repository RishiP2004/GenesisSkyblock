<?php

declare(strict_types=1);

namespace skyblock\misc\stronghold;

use jackmd\scorefactory\ScoreFactory;
use pocketmine\math\AxisAlignedBB;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;
use RedisClient\Pipeline\PipelineInterface;
use sb\islands\traits\IslandCallTrait;
use sb\player\CorePlayer;
use skyblock\Database;
use skyblock\islands\Island;
use skyblock\items\lootbox\LootboxItem;
use skyblock\traits\AetherSingletonTrait;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\ScoreboardUtils;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

abstract class Stronghold {
	use IslandCallTrait;

	const PREFIX = "§7[§l§dStrong§bhold§r§7] ";
	const CAPTURE_TIME = 100;

	const STATUS_CAPTURING = 1;
	const STATUS_CAPTURED = 2;
	const STATUS_ATTACKING = 3;

	protected AxisAlignedBB $bb;
	protected bool $hasActiveChest = false;

	public ?string $fullyCaptured = null;
	public ?string $cappingIsland = null;
	public ?string $attackers = null;
	public float $percentage = 0;
	public $capturetime = self::CAPTURE_TIME;
	public $capturetake = 0;

	public array $loottable = [];

	public function __construct() {
		$this->bb = $this->buildBoundingBox();

		foreach($this->buildLoottable() as $v){
			for($i = 1; $i <= $v->getChance(); $i++){
				$this->loottable[] = $v->getItem();
			}
		}
		$this->start();
	}

	public function tick(): void {
		//todo: make sure world is loaded?
		$inside = [];

		foreach($this->getWorld()->getNearbyEntities($this->bb) as $entity) {
			if($entity instanceof Player && $this->bb->isVectorInside($entity->getLocation())){
				$inside[strtolower($entity->getName())] = $entity;
			}
		}
		if(empty($inside)) {
			return;
		}
		$result = [];
		/**
		 * @var $p CorePlayer
		 */
		foreach($inside as $k => $p) {
			$result[] = $p->getIslandName();
		}
		$islands = [];
		$allKeys = array_keys($inside);

		foreach($result as $k => $v){
			if(isset($allKeys[$k])){
				if($v === null || $v === "") continue;

				$islands[$allKeys[$k]] = $v;
			}
		}
		if(count($islands) === 1) {
			//increase n stuff
			$isle = array_shift($islands);

			if ($this->cappingIsland === null) {
				$this->attackers = $isle;
				$this->cappingIsland = $isle;
			}

			if ($this->cappingIsland === $isle) {

				if($this->percentage < 100){
					$this->percentage += 1;
				}

				if($this->percentage > 100) {
					$this->percentage = 100;
				}

				if($this->fullyCaptured !== null){
					$this->attackers = null;
				}

				if($this->fullyCaptured === null && $this->percentage === (float) 100){
					$this->fullyCaptured = $this->cappingIsland;
					$this->attackers = null;
					Server::getInstance()->broadcastMessage(self::PREFIX . "§d§l{$this->fullyCaptured}§r §bhas just fully captured the §d§l{$this->getName()}§r§b stronghold");
				}


			} else {
				$this->percentage -= 1;

				if($this->percentage <= 0){
					$this->percentage = 0;
					if($this->fullyCaptured !== null){
						$name = $this->fullyCaptured;
						$this->fullyCaptured = null;
						Server::getInstance()->broadcastMessage(self::PREFIX . "§d§l$name §r§bhas lost control over the §d§l{$this->getName()}§r§b stronghold");
					} else {
						//captured
						$this->cappingIsland = $isle;
						Server::getInstance()->broadcastMessage(self::PREFIX . "§d§l{$isle}§r §bhas started taking control over the §d§l{$this->getName()}§r§b stronghold");
					}
				}
				$this->attackers = $isle;
				if($this->percentage === 70){
					if($this->fullyCaptured !== null) {
						$this->getIsland($this->fullyCaptured, function($island) {
							$island->announce(self::PREFIX . "§b§l{$this->attackers}§r§b is attacking the §d§l{$this->getName()}§r§b stronghold");
						});
					}
				}

				if($this->percentage === 50){
					if($this->fullyCaptured !== null){
						$this->getIsland($this->fullyCaptured, function($island) {
							$island->announce(self::PREFIX . "§b§l{$this->attackers}§r§b is attacking the §d§l{$this->getName()}§r§b stronghold");
						});
					}
				}

				if($this->percentage === 30){
					if($this->fullyCaptured !== null){
						$this->getIsland($this->fullyCaptured, function($island) {
							$island->announce(self::PREFIX . "§b§l{$this->attackers}§r§b is attacking the §d§l{$this->getName()}§r§b stronghold");
						});
					}
				}
				//losing cap
			}

		} elseif(count($islands) > 1) {
			$attacking = $islands[array_keys($islands)[0]];
			$contains = false;
			$trueAttackers = null;

			$lookingFor = ($this->fullyCaptured !== null ? $this->fullyCaptured : ($this->cappingIsland !== null ? $this->cappingIsland : null));


			if($lookingFor !== null){

				foreach ($islands as $key => $island){
					if($island === $lookingFor){
						$contains = true;
					} elseif($trueAttackers === null){
						$trueAttackers = $island;
					}
				}

			}

			if($trueAttackers === null){
				$trueAttackers = $attacking;
			}

			if($contains === true) {
				$this->attackers = $trueAttackers;
				return;
			}

			if($this->fullyCaptured !== null && $this->percentage > 0){
				$this->percentage -= 1;
				$this->attackers = $trueAttackers;
				return;
			}


			if($this->percentage <= 0 && $this->fullyCaptured !== null){
				$name = $this->fullyCaptured;
				$this->fullyCaptured = null;
				$this->cappingIsland = $trueAttackers;
				$this->attackers = $trueAttackers;
				Server::getInstance()->broadcastMessage(self::PREFIX . "§d§l$name §r§bhas lost control over the §d§l{$this->getName()}§r§b stronghold");
				return;
			}

			if($this->percentage <= 0 && $this->cappingIsland !== null && $this->cappingIsland !== $trueAttackers){
				$this->cappingIsland = $trueAttackers;
				$this->attackers = $trueAttackers;
				Server::getInstance()->broadcastMessage(self::PREFIX . "§d§l{$trueAttackers}§r §bhas started taking control over the §d§l{$this->getName()}§r§b stronghold");
				return;
			}

			$this->percentage += 1;
			$this->attackers = $trueAttackers;

			if($this->percentage >= 100){
				$this->fullyCaptured = $trueAttackers;
				$this->cappingIsland = $trueAttackers;
				$this->attackers = null;
				Server::getInstance()->broadcastMessage(self::PREFIX . "§d§l{$this->fullyCaptured}§r §bhas just fully captured the §d§l{$this->getName()}§r§b stronghold");
			}
		}

		foreach ($this->getWorld()->getPlayers() as $p){
			$this->updateScoreboard($p);
		}
	}

	public function updateScoreboard(Player $player): void  {
		$attackers = $this->attackers !== null ? $this->attackers : "N/A";
		$captured = $this->fullyCaptured !== null ? $this->fullyCaptured : "N/A";
		$status = $this->fullyCaptured !== null ? "§aControlled" : "§dControlling";

		if($this->attackers !== null) {
			$status = "§cAttacking";
		}
		//todo
		//ScoreFactory::setScoreLine($player, ScoreboardUtils::LINE_STRONGHOLD_OPEN, $this->getName());
		//ScoreboardUtils::setLine($player, ScoreboardUtils::LINE_STRONGHOLD_CLOSE, "");
		//ScoreboardUtils::setLine($player, ScoreboardUtils::LINE_STRONGHOLD_STATUS, $status);
		//ScoreboardUtils::setLine($player, ScoreboardUtils::LINE_STRONGHOLD_CONTROLLED_BY, $captured);
		//ScoreboardUtils::setLine($player, ScoreboardUtils::LINE_STRONGHOLD_PERCENTAGE, "$this->percentage");
		//ScoreboardUtils::setLine($player, ScoreboardUtils::LINE_STRONGHOLD_ATTACKERS, $attackers);
	}

	public function getWorld(): ?World {
		Server::getInstance()->getWorldManager()->loadWorld($this->getWorldName());

		return Server::getInstance()->getWorldManager()->getWorldByName($this->getWorldName());
	}

	public function close(): void {}

	abstract public function getWorldName(): string;
	abstract public function buildBoundingBox(): AxisAlignedBB;
	abstract public function getName(): string;

	/**
	 * @return LootboxItem[]
	 */
	abstract public function buildLoottable(): array;
}