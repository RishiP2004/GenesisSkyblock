<?php

declare(strict_types = 1);

namespace sb\entity;

use pocketmine\block\VanillaBlocks;
use pocketmine\color\Color;
use pocketmine\entity\effect\InstantEffect;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\SplashPotion;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\PotionType;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\particle\PotionSplashParticle;
use pocketmine\world\sound\PotionSplashSound;
use pocketmine\player\Player;

class CustomPotion extends SplashPotion {
	public const MAX_HIT = 1.0325;
	public const MAX_MISS = 0.9025;
	protected float $gravity = 0.065;
	protected float $drag = 0.0025;

	public function __construct(Location $location, ?Entity $shootingEntity, PotionType $potionType, ?CompoundTag $nbt = null) {
		parent::__construct($location, $shootingEntity, $potionType, $nbt);
		$this->setScale(0.4);
	}

	protected function onHit(ProjectileHitEvent $event) : void {
		$effects = $this->getPotionEffects();
		$hasEffects = true;
		if(count($effects) === 0) {
			$particle = new PotionSplashParticle(PotionSplashParticle::DEFAULT_COLOR());
			$hasEffects = false;
		} else {
			$colors = [];
			foreach($effects as $effect) {
				$level = $effect->getEffectLevel();
				for($j = 0; $j < $level; ++$j) {
					$colors[] = $effect->getColor();
				}
			}
			$particle = new PotionSplashParticle(Color::mix(...$colors));
		}
		$this->getWorld()->addParticle($this->location, $particle);
		$this->broadcastSound(new PotionSplashSound());
		if($hasEffects) {
			if($event instanceof ProjectileHitEntityEvent) {
				$entityHit = $event->getEntityHit();
				if($entityHit instanceof Player) {
					foreach($this->getPotionEffects() as $effect) {
						if(!$effect->getType() instanceof InstantEffect) {
							$newDuration = (int) round($effect->getDuration() * 1.0 * self::MAX_HIT);
							if($newDuration < 20) {
								continue;
							}
							$effect->setDuration($newDuration);
							$entityHit->getEffects()->add($effect);
						} else {
							$effect->getType()->applyEffect($entityHit, $effect, self::MAX_HIT, $this);
						}
					}
					foreach($this->getWorld()->getNearbyEntities($this->getBoundingBox()->expand(1.75, 3, 1.75)) as $nearby) {
						if($nearby instanceof Player and $entityHit->getId() !== $nearby->getId()) {
							$array[$nearby->getName()] = $nearby;
						}
					}
					if(isset($array) and is_array($array)) {
						$this->doNearbyCheck($array);
					}
				}
			} else if($event instanceof ProjectileHitBlockEvent) {
				$this->doNearbyCheck($this->getWorld()->getNearbyEntities($this->getBoundingBox()->expand(1.75, 3, 1.75)));
				if($this->getPotionType()->equals(PotionType::WATER())) {
					$blockIn = $event->getBlockHit()->getSide($event->getRayTraceResult()->getHitFace());
					if($blockIn->getTypeId() === VanillaBlocks::FIRE()->getTypeId()) {
						$this->getWorld()->setBlock($blockIn->getPosition(), VanillaBlocks::AIR());
					}
					foreach($blockIn->getHorizontalSides() as $horizontalSide) {
						if($horizontalSide->getTypeId() === VanillaBlocks::FIRE()->getTypeId()) {
							$this->getWorld()->setBlock($horizontalSide->getPosition(), VanillaBlocks::AIR());
						}
					}
				}
			}
		}
	}

	private function doNearbyCheck(?array $entities = null) : void {
		foreach(array_unique($entities) as $entity) {
			if($entity instanceof Player) {
				foreach($this->getPotionEffects() as $effect) {
					if(!$effect->getType() instanceof InstantEffect) {
						$newDuration = (int) round($effect->getDuration() * 0.75 * self::MAX_MISS);
						if($newDuration < 20) {
							continue;
						}
						$effect->setDuration($newDuration);
						$entity->getEffects()->add($effect);
					} else {
						$effect->getType()->applyEffect($entity, $effect, self::MAX_MISS, $this);
					}
				}
			}
		}
	}

	public function entityBaseTick(int $tickDiff = 1) : bool {
		if($this->isCollided) {
			$this->flagForDespawn();
		}
		return parent::entityBaseTick($tickDiff);
	}
}