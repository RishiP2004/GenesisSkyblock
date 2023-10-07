<?php

namespace sb\world\koth;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\math\AxisAlignedBB;
use sb\player\CorePlayer;
use sb\utils\MathUtils;

class Koth {
	private AxisAlignedBB $aaBB;
	/** @var int[] */
	private array $timers = [];

	private float $uptime = 0;
	private bool $running = false;

	public function __construct(
		private readonly string  $name,
		private readonly Position $pos1,
		private readonly Position $pos2,
		private readonly int     $autoTime,
		private readonly int     $winTime,
		private readonly array   $winCommands,
		private readonly array $rewards
	) {
		$this->recalculateBoundingBox();
	}

	public function recalculateBoundingBox(): void {
		$this->aaBB = MathUtils::fromCoordinates($this->pos1->asVector3(), $this->pos2->asVector3());
		$this->aaBB->expand(0.0001, 0.0001, 0.0001);
		$this->aaBB->maxX += 1;
		$this->aaBB->maxZ += 1;
	}

	public function getName() : string {
		return $this->name;
	}

	public function getPos1() : Position {
		return $this->pos1;
	}

	public function getPos2() : Position {
		return $this->pos2;
	}

	public function getAutoTime() : int {
		return $this->autoTime;
	}

	public function getWinTime() : int {
		return $this->winTime;
	}

	public function isRunning(): bool {
		return $this->running;
	}

	public function getWinCommands() : array {
		return $this->winCommands;
	}

	public function getRewards() : array {
		return $this->rewards;
	}

	public function start() : void {
		$this->running = true;
		$this->timers = [];
		$this->uptime = 0;

		Server::getInstance()->broadcastMessage(Koths::PREFIX . "     KoTH: " . TextFormat::RED . $this->getName() . " has just been enabled!");
	}

	public function getTimers() : array {
		return $this->timers;
	}

	public function getUptime() : int {
		return $this->uptime;
	}

	public function stop() : void {
		$this->running = false;
		$this->timers = [];
		$this->uptime = 0;
	}

	public function tick() : void {
		$seen = [];

		[$oldKing, $oldTime] = $this->getCurrentKing();

		foreach($this->pos1->getWorld()->getNearbyEntities(clone $this->aaBB) as $entity) {
			if(!$entity instanceof CorePlayer || !$entity->isAlive() || !$this->aaBB->isVectorInside($entity->getPosition()->floor()) || $entity->isCreative()) continue;

			$timeRemaining = $this->timers[$entity->getName()] = ($this->timers[$entity->getName()] ?? $this->getWinTime()) - 0.5;

			if($entity->getKoth() == null) $entity->setKoth($this);

			$seen[$entity->getName()] = true;
			[$newKing, $_] = $this->getCurrentKing();

			if($timeRemaining <= 0) {
				foreach($this->getRewards() as $reward) {
					$entity->getInventory()->canAddItem($reward) ? $entity->getInventory()->addItem($reward) : $entity->getWorld()->dropItem($entity->getPosition()->asVector3(), $reward);
				}
				foreach($this->getWinCommands() as $cmd) {
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), str_replace(
						["{PLAYER}", "{KOTH}"],
						[$entity->getName(), $this->name],
						$cmd
					), true);

				}
				Server::getInstance()->broadcastMessage(Koths::PREFIX . $entity->getName() . " won the KoTH " . $this->name . " that was up for " .  MathUtils::secondsToTime((int) $this->uptime)["h"] . " hours and " . MathUtils::secondsToTime((int) $this->uptime)["s"] . " seconds");
				$this->stop();
			}
		}
		foreach($this->timers as $k => $timer) {
			if(!isset($seen[$k])) {
				$p = Server::getInstance()->getPlayerExact($k);
				if($p instanceof CorePlayer) {
					$p->setKoth(null);
					unset($this->timers[$k]);
				}
			}
		}
		asort($this->timers);

		if($this->isRunning()) {
			[$newKing, $_] = $this->getCurrentKing();
			$oldKingTimeInside = $this->getWinTime() - $oldTime;

			if($newKing === null && $oldKing !== null && $oldKingTimeInside > 30) {
				Server::getInstance()->broadcastMessage(Koths::PREFIX . "The KoTH " . $this->name . " is being captured by " . $oldKing->getName());
			}
			if($oldKing !== null && $newKing !== null && $oldKing !== $newKing && $oldKingTimeInside > 30) {
				Server::getInstance()->broadcastMessage(Koths::PREFIX . "The KoTH " . $this->name . " has a new KING: " . $newKing->getName());
			}
			if($oldKing === null && $newKing !== null) {
				Server::getInstance()->broadcastMessage(Koths::PREFIX . "The KoTH " . $this->name . " has a new KING: " . $newKing->getName());
			}
		}
		$this->uptime += 0.5;
	}

	public function getCurrentKing() : array {
		foreach($this->timers as $name => $time) {
			$player = Server::getInstance()->getPlayerExact($name);
			return [$player, $time];
		}
		return [null, $this->getWinTime()];
	}
}