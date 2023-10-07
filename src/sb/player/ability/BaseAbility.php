<?php

namespace sb\player\ability;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Entity;
use pocketmine\scheduler\ClosureTask;
use sb\player\CorePlayer;
use sb\Skyblock;
use sb\utils\MathUtils;

abstract class BaseAbility {
    protected array $cooldowns = [];

    public function __construct(
		private readonly string $name = "",
        private readonly float $chance = 0,
        private readonly int   $cooldown = 0,
    ){}

    public function getName() : string {
        return $this->name;
    }

    public function getChance() : float {
        return $this->chance;
    }

    public function getCooldown() : float {
        return $this->cooldown;
    }

    abstract public function react(CorePlayer $player, ...$args): void;

    public function attemptReact(Entity $entity, ...$args) : bool {
        if (!($entity instanceof CorePlayer)) return false;

        $name = $entity->getName();
        $cooldown = $this->cooldowns[$name] ?? null;

        if ($cooldown === null) {
            $cooldown = MathUtils::getRandomFloat(0, 100);
            if ($cooldown <= $this->getChance()) {
                $item = $entity->wearFullArmorSet();

                //if ($item instanceof BaseArmor) $armorName = $item->getColoredName();
                //else $armorName = ucwords($this->getName());
                
                $this->cooldowns[$name] = $this->getName();
                $this->react($entity, ...$args);

                Skyblock::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($name): void{
                    unset($this->cooldowns[$name]);
                }), $this->getCoolDown() * 20);
                return true;
            }
        }
        return false;
    }

    public function attack(EntityDamageEvent $event) : bool {
        if ($event instanceof EntityDamageByEntityEvent) {
            if ($event->getDamager() instanceof CorePlayer) {
                if (!$this->attemptReact($event->getDamager())) {
                    return false;
                }
            }
        }
        return true;
    }
}