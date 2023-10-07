<?php

declare(strict_types=1);

namespace sb\entity;

use pocketmine\block\Water;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Projectile;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\utils\Random;
use pocketmine\world\particle\BubbleParticle;
use sb\item\FishingRod;
use sb\player\CorePlayer;

final class FishingHook extends Projectile {

	/** @var float */
	public float $height = 0.25;
	/** @var float */
	public float $width = 0.25;

	/** @var float */
	protected float $gravity = 0.1;

	private int $maxFishTime = 300 * 20;
	private int $fishTime = 0;
	private bool $active = false;
	private int $time = 0;
	private int $range = 12;

	protected function initEntity(CompoundTag $nbt): void
	{
		parent::initEntity($nbt);
		$this->setHealth(100);

		$this->range = 30;
		$this->setCanSaveWithChunk(false);
	}

	protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult) : void{
		$this->flagForDespawn();
	}

	public function handleHookCasting(Vector3 $motion, float $f1, float $f2): void {
		$x = $motion->getX();
		$y = $motion->getY();
		$z = $motion->getZ();

		$rand = new Random();
		$f = sqrt($x * $x + $y * $y + $z * $z);
		$x /= $f;
		$y /= $f;
		$z /= $f;
		$x += $rand->nextSignedFloat() * 0.007499999832361937 * $f2;
		$y += $rand->nextSignedFloat() * 0.007499999832361937 * $f2;
		$z += $rand->nextSignedFloat() * 0.007499999832361937 * $f2;
		$x *= $f1;
		$y *= $f1;
		$z *= $f1;
		$this->motion->x += $x;
		$this->motion->y += $y;
		$this->motion->z += $z;
	}

	public function getResultDamage(): int {
		return 1;
	}

	public function setMaxFishTime(float|int $maxFishTime) : void{
		$this->maxFishTime = $maxFishTime;
	}

	public function onUpdate(int $currentTick): bool {
		if($this->isClosed() || $this->isFlaggedForDespawn()){
			return false;
		}
		$owner = $this->getOwningEntity();

		if ($owner instanceof CorePlayer) {
			if (!$owner->isAlive() || $owner->isClosed() || !$owner->getInventory()->getItemInHand() instanceof FishingRod) {
				$this->flagForDespawn();
			}
			$underwater = $this->isUnderwater();

			if($this->active === true) {
				if(++$this->time >= 40) {
					$this->active = false;
					$this->time = 0;
					$this->fishTime = 0;
				}
				$this->getWorld()->addParticle($this->getLocation(), new BubbleParticle());
			}

			if($this->getLocation()->distance($owner->getLocation()) >= $this->range){
				$this->flagForDespawn();
			}

			if ($underwater === true) {
				$this->location->y += 0.3;
			}

			if($this->inWater()){
				$this->fishTime += 3;
				if($this->fishTime >= $this->maxFishTime){
					$this->fishTime = 0;
					$this->time = 0;
					$this->active = true;
					$owner->sendTip("§b§lFish caught!");
				}
			}
		}
		else {
			$this->flagForDespawn();
		}
		return parent::onUpdate($currentTick);
	}

	public function getSecondsLeft(): float {
		$ticksLeft = $this->maxFishTime - $this->fishTime;

		return round($ticksLeft / 20, 1);
	}

	public function inWater(): bool {
		return $this->getWorld()->getBlock($this->getLocation()) instanceof Water;
	}


	public function setActive(bool $active): void {
		$this->active = $active;
		$this->time = 0;
		$this->fishTime = 0;
	}


	public function isActive(): bool {
		return $this->active;
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo($this->height, $this->width);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::FISHING_HOOK;
	}

	protected function getInitialDragMultiplier() : float {
		return 0.02;
	}

	protected function getInitialGravity() : float {
		return 0.05;
	}
}