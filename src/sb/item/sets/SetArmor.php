<?php

namespace sb\item\sets;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\Arrow;
use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use sb\item\CustomItem;
use sb\item\listeners\ItemTakeDamageListener;
use sb\item\utils\ArmorUtils;
use sb\item\utils\DamageInfo;
use sb\item\CustomItems;
use sb\player\CorePlayer;

class SetArmor extends CustomItem implements ItemTakeDamageListener {
	public function __construct(private readonly string $name, private readonly string $id, private readonly Item $item, private readonly ?DamageInfo $damageInfo = null) {
		self::addNameTag($item);
	}

	public function getItem(): Item{
		return $this->item;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getId() : string {
		return $this->id;
	}

	public function getColoredName() : string {
		return match ($this->getName()) {
			"Dragon" . strtoupper(self::getArmorType()) => "§eDragon",
			"Cupid" . strtoupper(self::getArmorType()) => "§dCupid",
			"Fantasy" . strtoupper(self::getArmorType()) => "§2Fantasy",
			"Koth" . strtoupper(self::getArmorType()) => "§l§f§k|§r §l§cK§6.§eO§a.§bT§5.§dH§r",
			"Phantom" . strtoupper(self::getArmorType()) => "§l§cPhantom",
			"Ranger" . strtoupper(self::getArmorType()) => "§l§aRanger",
			"Reaper" . strtoupper(self::getArmorType()) => "§l§4Reaper",
			"Spooky" . strtoupper(self::getArmorType()) => "§l§6Spooky",
			"Supreme" . strtoupper(self::getArmorType()) => "§l§4Supreme",
			"Thor" . strtoupper(self::getArmorType()) => "§l§bThor",
			"Traveler" . strtoupper(self::getArmorType()) => "§l§5Traveler",
			"Xmas" . strtoupper(self::getArmorType()) => "§l§cX§2M§aA§fS§r",
			"Yeti" . strtoupper(self::getArmorType()) => "§l§bYeti",
			"Yijki" . strtoupper(self::getArmorType()) => "§l§fYijki",
		};
	}

    public function getArmorType() : string {
        if (!$this->item instanceof Armor) return "undefined";

        return ArmorUtils::armorSlotToType($this->item->getArmorSlot());
    }

	public function getDamageInfo(): DamageInfo {
		if(is_null($this->damageInfo)) return new DamageInfo([]);
		else return $this->damageInfo;
	}

	public function onTakeDamage(Item $item, CorePlayer $damaged, EntityDamageEvent $event) : void {
		$item2 = $damaged->wearFullArmorSet();

		if($item2 instanceof SetArmor) {
			$this->defend($event);

			if($event instanceof EntityDamageByEntityEvent) {
				$this->attack($event);

				if ($event->getDamager() instanceof Arrow) {
					if ($event->getDamager()->getOwningEntity() instanceof CorePlayer) {
						$damageArmor = $event->getDamager()->getOwningEntity()->wearFullArmorSet();

						if ($damageArmor instanceof SetArmor) $damageArmor->attack($event);

						$itemInHand = $damaged->getInventory()->getItemInHand();

						if(($string = $itemInHand->getNamedTag()->getString(CustomItem::TAG_CUSTOM_ITEM, "")) !== "") {
							$i = CustomItems::fromString($string);

							if($i instanceof SetWeapon) {
								if ($i->getName() === $item2->getName()) $i->attack($event);
							}
						}
					}
				}
			}
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
}