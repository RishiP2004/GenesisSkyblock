<?php

namespace sb\item\utils;

use pocketmine\utils\TextFormat as T;
use pocketmine\utils\EnumTrait;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-annotations.php
 * @generate-registry-docblock
 *
 * @method static LootboxType UNRAIDABLE()
 */

final class LootboxType {
	use EnumTrait {
		__construct as Enum___construct;
	}

	protected static function setup() : void{
		self::registerAll(
			new self("unraidable", "Unraidable", "&c&lUnraidable",
				[
					"",
					T::colorize("&r&7Hey whats up guys Qcy here"),
					T::colorize("&r&7back with the Qcy 63.0 Cell Defence!"),
					T::colorize("&r&7This defence is unraidable"),
					T::colorize("&r&8Released: Jan 31, 2020"),
					T::colorize("&f"),
					T::colorize("&r&f&lRandom Loot (&r&74 items&r&f&l)"),
				],
			[

			]
			),
		);
	}

	private function __construct(
		string $enumName,
		private readonly string $displayName,
		private readonly string $customName,
		private readonly array  $lore,
		private readonly array  $rewards
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

	public function getLore() : array {
		return $this->lore;
	}

	public function getRewards() : array {

	}
}