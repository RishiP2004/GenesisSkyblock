<?php

namespace sb\item\utils;

use pocketmine\item\Axe;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\FlintSteel;
use pocketmine\item\Hoe;
use pocketmine\item\Pickaxe;
use pocketmine\item\Shears;
use pocketmine\item\Shovel;
use pocketmine\item\Sword;
use pocketmine\item\Tool;

final class ToolUtils {
	public const TOOL_TO_ITEMFLAG = [
		Pickaxe::class => ItemFlags::PICKAXE,
		Sword::class => ItemFlags::SWORD,
		Axe::class => ItemFlags::AXE,
		Hoe::class => ItemFlags::HOE,
		Shovel::class => ItemFlags::SHOVEL,
		Bow::class => ItemFlags::BOW,
		FlintSteel::class => ItemFlags::FLINT_AND_STEEL,
		Shears::class => ItemFlags::SHEARS,
	];

	public static function getToolItemFlag(Tool $item): int {
		foreach(ToolUtils::TOOL_TO_ITEMFLAG as $class => $itemFlag) {
			if($item instanceof $class) return $itemFlag;
		}
		throw new \UnexpectedValueException("Unknown item type " . get_class($item));
	}
}