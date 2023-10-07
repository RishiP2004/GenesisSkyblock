<?php

namespace sb\item\utils;

use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\utils\EnumTrait;
use sb\item\enchantment\CERarity;
use sb\item\enchantment\CustomEnchantments;
use sb\player\CorePlayer;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-annotations.php
 * @generate-registry-docblock
 *
 * @method static RarityType UNCOMMON()
 * @method static RarityType RARE()
 * @method static RarityType ELITE()
 * @method static RarityType MASTER()
 * @method static RarityType LEGENDARY()
 * @method static RarityType HEROIC()
 *
 */
class RarityType {
	use EnumTrait {
		__construct as Enum___construct;
	}

	protected static function setup() : void{
		self::registerAll(
			new self("uncommon", "Uncommon", CERarity::UNCOMMON, "§8Uncommon", "§8"),
			new self("rare", "Rare", CERarity::RARE, "§2Rare", "§2"),
			new self("elite", "Elite", CERarity::ELITE, "§5Elite", "§5"),
			new self("master", "Master", CERarity::MASTERY, "§aMaster", "§a"),
			new self("legendary", "Legendary", CERarity::LEGENDARY, "§eLegendary", "§r"),
			new self("heroic", "Heroic", CERarity::HEROIC, "§6Heroic", "§6"),
		);
	}

	private function __construct(
		string                  $enumName,
		private readonly string $displayName,
		private readonly int    $id,
		private readonly string $customName,
		private readonly string $color,
	){
		$this->Enum___construct($enumName);
	}

	public static function fromString(string $name) : self {
		$result = self::_registryFromString($name);
		assert($result instanceof self);
		return $result;
	}

	public static function fromId(int $id) : self {
		$result = self::_registryGetAll();

		foreach($result as $r) {
			if($r->getId() == $id) {
				assert($r instanceof self);
				return $r;
			}
		}
	}

	public function getDisplayName() : string {
		return $this->displayName;
	}

	public function getId() : int{
		return $this->id;
	}

	public function getCustomName() : string {
		return $this->customName;
	}

	public function getColor() : string {
		return $this->color;
	}

	public function giveRandomEnchantBook(Item $item, CorePlayer $player, bool $agreement = true, int $success = 100, int $destroy = 50): void {
		$randomEnchant = [];

		foreach (CustomEnchantments::getAll() as $enchantment) {
			if($enchantment->getRarity() == $this->getId()) {
				$randomEnchant[] = $enchantment;
			}
		}
		$enchant = $randomEnchant[array_rand($randomEnchant)];
		$message = "§l§e(!) §r§7The {$this->getCustomName()} §r§7Thebook gave you.. ";

		$level = mt_rand(1, $enchant->getMaxLevel());
		$e = new EnchantmentInstance($enchant, $level);

		$enchantBook = VanillaItems::BOOK(); //todo: create a custom item identifier for this

		if ($agreement) {
			$en = $enchant->getLoreLine($level);
			$player->sendMessage($message . $en);
		}
		$player->getInventory()->canAddItem($enchantBook) ? $player->getInventory()->addItem($enchantBook) : $player->getWorld()->dropItem($player->getPosition()->asVector3(), $enchantBook);
	}
}