<?php

namespace sb\item\sets;

use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Event;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\world\sound\ItemBreakSound;
use sb\item\CustomItem;
use sb\item\listeners\ItemDamageListener;
use sb\item\utils\DamageInfo;
use sb\player\CorePlayer;

class SetWeapon extends CustomItem implements ItemDamageListener {
	public function __construct(private readonly string $name, private readonly string $id, private readonly Item $item, private ?DamageInfo $damageInfo = null) {
		self::addNameTag($item);
    }

	public function getItem(): Item {
		return $this->item;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getDamageInfo(): DamageInfo {
		if (is_null($this->damageInfo)) {
			return new DamageInfo([]);
		} else {
			return $this->damageInfo;
		}
	}

	public function onDamage(Item $item, CorePlayer $damager, EntityDamageEvent $event) : void {
		$this->defend($event);

		if($event instanceof EntityDamageByEntityEvent) $this->attack($event);
	}

	public function onUse(CorePlayer $player, Event $event, Item $item): void {
		if($event instanceof EntityDamageEvent) {
			$this->defend($event);

			if($event instanceof EntityDamageByEntityEvent) $this->attack($event);
		}
	}

    public function attack(EntityDamageEvent $event): void{
        $info = $this->getDamageInfo();
        if ($info->getIncrease($event->getEntity()::class) > 0) {
            $event->setModifier(($event->getFinalDamage() * $info->getIncrease($event->getEntity())), DamageInfo::CUSTOM_MODIFIER);
        }
    }

    public function defend(EntityDamageEvent $event): void{
        $info = $this->getDamageInfo();
        if ($info->getDecrease($event->getEntity()::class) > 0) {
            $event->setModifier(-($event->getFinalDamage() * $info->getDecrease($event->getEntity())), DamageInfo::CUSTOM_MODIFIER);
        }
    }

    public final function damageArmor(float $damage, Human $entity) : void{
        $durabilityRemoved = (int) max(floor($damage / 4), 1);

        $armor = $entity->getArmorInventory()->getContents(true);
        foreach ($armor as $item){
            if ($item instanceof Armor){
                $this->damageItem($item, $durabilityRemoved, $entity);
            }
        }

        $entity->getArmorInventory()->setContents($armor);
    }

    public final function damageItem(Durable $item, int $durabilityRemoved, Human $entity) : void{
        $item->applyDamage($durabilityRemoved);

        if ($item->isBroken()) {
            $entity->broadcastSound(new ItemBreakSound());
        }
    }

	public function getId() : string {
		return $this->id;
	}
}