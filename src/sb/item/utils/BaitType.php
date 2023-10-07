<?php

namespace sb\item\utils;

use pocketmine\item\VanillaItems;
use pocketmine\utils\EnumTrait;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-annotations.php
 * @generate-registry-docblock
 *
 * @method static BaitType ARMOR()
 * @method static BaitType WEAPON()
 * @method static BaitType CACHE()
 * @method static BaitType POUCH()
 */

final class BaitType {
	use EnumTrait {
		__construct as Enum___construct;
	}

	protected static function setup() : void{
		self::registerAll(
			new self("armor", "Armor", "§bArmor", [
				VanillaItems::DIAMOND_HELMET(),
				VanillaItems::DIAMOND_BOOTS()
			]),
			new self("weapon", "Weapon", "§4Weapon", [
				VanillaItems::DIAMOND_SWORD(),
				VanillaItems::DIAMOND_AXE()
			]),
			new self("cache", "Cache", "§7Cache", [

			]),
			new self("pouch", "Pouch","§aPouch", [

			]),
		);
	}

	private function __construct(
		string                  $enumName,
		private readonly string $displayName,
		private readonly string $customName,
		private readonly array  $rewardsToFocus
	){
		$this->Enum___construct($enumName);
	}


	public static function fromString(string $name): self {
		$result = self::_registryFromString($name);
		assert($result instanceof self);
		return $result;
	}

	public function getDisplayName() : string{
		return $this->displayName;
	}

	public function getCustomName() : string{
		return $this->customName;
	}

	public function getRewardsToFocus() : array {
		return $this->rewardsToFocus;
	}
}