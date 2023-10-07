<?php

namespace sb\world\prosperity;

use pocketmine\world\Position;
use pocketmine\math\AxisAlignedBB;
use sb\player\CorePlayer;
use sb\utils\MathUtils;

class Prosperity {
	private AxisAlignedBB $aaBB;
	/** @var int[] */
	private array $timers = [];

	public function __construct(
		private readonly string  $name,
		private readonly Position $pos1,
		private readonly Position $pos2,
		private readonly int     $tick,
		private readonly int   $money,
		private readonly float   $xp
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

	public function getTick() : int {
		return $this->tick;
	}

	public function getMoney() : int {
		return $this->money;
	}

	public function getXp(): float {
		return $this->xp;
	}

	public function tick() : void {
		$seen = [];

		foreach($this->pos1->getWorld()->getNearbyEntities(clone $this->aaBB) as $entity) {
			if(!$entity instanceof CorePlayer || !$entity->isAlive() || !$this->aaBB->isVectorInside($entity->getPosition()->floor()) || $entity->isCreative())
				continue;
			
			$timeRemaining = $this->timers[$entity->getName()] = ($this->timers[$entity->getName()] ?? $this->getTick()) - 0.5;

			$seen[$entity->getName()] = true;

			if($timeRemaining <= 0) {
				$entity->sendActionBarMessage("§l§a +$" . $this->getMoney() . " §b+Exp " . $this->getXp());
				$entity->getCoreUser()->addMoney($this->getMoney());
				$entity->getXpManager()->addXp($this->getXp());
				$this->timers[$entity->getName()] = ($this->getTick()) - 0.5;
			}
		}
		foreach($this->timers as $k => $timer) {
			if(!isset($seen[$k]))
				unset($this->timers[$k]);
		}
	}
}