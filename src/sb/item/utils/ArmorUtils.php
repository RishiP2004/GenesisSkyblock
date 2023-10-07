<?php

namespace sb\item\utils;

use pocketmine\inventory\ArmorInventory;
use pocketmine\item\enchantment\ItemFlags;

final class ArmorUtils {
	public const ARMOR_SLOT_TO_ITEMFLAG = [
		ArmorInventory::SLOT_HEAD => ItemFlags::HEAD,
		ArmorInventory::SLOT_CHEST => ItemFlags::TORSO,
		ArmorInventory::SLOT_LEGS => ItemFlags::LEGS,
		ArmorInventory::SLOT_FEET => ItemFlags::FEET,
	];

	public static function armorSlotToType(int $slot): string{
		return match ($slot) {
			ArmorInventory::SLOT_HEAD => "helmet",
			ArmorInventory::SLOT_CHEST => "chestplate",
			ArmorInventory::SLOT_LEGS => "leggings",
			ArmorInventory::SLOT_FEET => "boots",
			default => "undefined"
		};
	}
}
