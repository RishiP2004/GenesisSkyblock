<?php

namespace sb\item\enchantment;

use muqsit\simplepackethandler\SimplePacketHandler;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\EventPriority;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use sb\item\enchantment\tool\AutoSmeltEnchantment;
use sb\item\enchantment\weapon\ExecuteEnchantment;
use sb\item\enchantment\weapon\InquisitiveEnchantment;
use sb\item\enchantment\CustomEnchantmentIdentifiers as CustomEnchantIds;
use pocketmine\item\Item;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\InventoryContentPacket;
use pocketmine\network\mcpe\protocol\InventorySlotPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\utils\RegistryTrait;
use pocketmine\utils\TextFormat;
use pocketmine\nbt\tag\CompoundTag;
use sb\item\enchantment\tool\ExperienceEnchantment;
use sb\item\enchantment\weapon\UnkillableEnchantment;
use sb\item\utils\RarityType;
use sb\Skyblock;

/**
 * @see build/generate-registry-annotations.php
 * @generate-registry-docblock
 * @method static CustomEnchantment HASTE()
 * @method static ExperienceEnchantment EXPERIENCE()
 */
final class CustomEnchantments {
	use RegistryTrait;

	//Used for name to id conversion.
	public static array $ids = [];
	//Used to find all enchants by rarity.
	public static array $rarities = [];

	public function __construct() {
		self::setup();
	}

	protected static function setup() : void {
		SimplePacketHandler::createInterceptor(Skyblock::getInstance(), EventPriority::HIGH)
			->interceptOutgoing(function(InventoryContentPacket $pk, NetworkSession $destination): bool {
				foreach ($pk->items as $i => $item) {
					$pk->items[$i] = new ItemStackWrapper($item->getStackId(), self::display($item->getItemStack()));
				}
				return true;
			})
			->interceptOutgoing(function(InventorySlotPacket $pk, NetworkSession $destination): bool {
				$pk->item = new ItemStackWrapper($pk->item->getStackId(), self::display($pk->item->getItemStack()));
				return true;
			})
			->interceptOutgoing(function(InventoryTransactionPacket $pk, NetworkSession $destination): bool {
				$transaction = $pk->trData;

				foreach ($transaction->getActions() as $action) {
					$action->oldItem = new ItemStackWrapper($action->oldItem->getStackId(), self::filter($action->oldItem->getItemStack()));
					$action->newItem = new ItemStackWrapper($action->newItem->getStackId(), self::filter($action->newItem->getItemStack()));
				}
				return true;
			});
		EnchantmentIdMap::getInstance()->register(CustomEnchantIds::FAKE_ENCH_ID, new Enchantment("", -1, 1, ItemFlags::ALL, ItemFlags::NONE));

		self::registerUncommon();
		self::registerRare();
		self::registerElite();
		self::registerLegendary();
	}

	protected static function registerUncommon() : void {
		self::register(
			"EXPERIENCE",
			CustomEnchantmentIdentifiers::EXPERIENCE,
			new ExperienceEnchantment(
				"Experience",
				CERarity::UNCOMMON,
				"Increase experience gained from broken blocks",
				1,
				ItemFlags::PICKAXE | ItemFlags::AXE | ItemFlags::SHOVEL,
			)
		);
	}

	protected static function registerRare() : void {
		self::register(
			"HASTE",
			CustomEnchantmentIdentifiers::HASTE,
			new ItemHeldEffectsEnchantment(
				"Haste", CERarity::RARE, "Allows you to swing tools faster",
				4, ItemFlags::PICKAXE | ItemFlags::SHOVEL | ItemFlags::AXE, ItemFlags::NONE,
				[new EffectInstance(VanillaEffects::HASTE())]
			)
		);
	}

	protected static function registerElite() : void {
		self::register(
			"AUTOSMELT",
			CustomEnchantmentIdentifiers::AUTOSMELT,
			new AutoSmeltEnchantment(
				"Auto Smelt", CERarity::ELITE, "Automatically smelt ores to ingots",
				1, ItemFlags::PICKAXE, ItemFlags::NONE,
			)
		);
		self::register(
			"EXECUTE",
			CustomEnchantmentIdentifiers::EXECUTE,
			new ExecuteEnchantment(
				"Execute", CERarity::ELITE, "A (Level * 4)% Chance to deal massive damage on enemy players with less than 45% HP.",
				5, ItemFlags::SWORD, ItemFlags::NONE,
			)
		);
		self::register(
			"SLOWNESS",
			CustomEnchantmentIdentifiers::SLOWNESS,
			new ExecuteEnchantment(
				"Slowness", CERarity::ELITE, "Chance to apply slowness to enemy",
				3, ItemFlags::SWORD, ItemFlags::NONE,
			)
		);
	}

	protected static function registerLegendary() : void {
		self::register(
			"INQUISITIVE",
			CustomEnchantmentIdentifiers::INQUISITIVE,
			new InquisitiveEnchantment(
				"Inquisitive", CERarity::RARE, "Increases the amount of vanilla XP gained from killing mobs.",
				4, ItemFlags::SWORD, ItemFlags::NONE
			)
		);
	}

	protected static function register(string $name, int $id, CustomEnchantment $enchantment) : void {
		$map = EnchantmentIdMap::getInstance();
		$map->register($id, $enchantment);
		StringToEnchantmentParser::getInstance()->register($enchantment->getName(), fn() => $enchantment); //todo: needed?

		self::$ids[$enchantment->getName()] = $id;
		self::$rarities[$enchantment->getRarity()][] = $id;
		self::_registryRegister($name, $enchantment);
	}

	public static function getIdFromName(string $name) : ?int {
		return self::$ids[$name] ?? null;
	}

	public static function getAll() : array{
		/**
		 * @var CustomEnchantment[] $result
		 * @phpstan-var array<string, CustomEnchantment> $result
		 */
		$result = self::_registryGetAll();
		return $result;
	}

	public static function getAllForRarity(RarityType $type) : array {
		return self::$rarities[$type->getId()];
	}

	public static function getRomanNumeral(int $level) : string {
		static $romanNumerals = [
			1 => "I", 2 => "II", 3 => "III", 4 => "IV", 5 => "V",
			6 => "VI", 7 => "VII", 8 => "VII", 9 => "IX", 10 => "X"
		];
		return ($romanNumerals[$level] ?? ((string)$level));
	}

	public static function display(ItemStack $itemStack) : ItemStack {
		$item = TypeConverter::getInstance()->netItemStackToCore($itemStack);

		if (count($item->getEnchantments()) > 0) {
			$additionalInformation = "";
			foreach ($item->getEnchantments() as $enchantmentInstance) {
				$enchantment = $enchantmentInstance->getType();
				if ($enchantment instanceof CustomEnchantment) {
					$additionalInformation .= "\n" . TextFormat::RESET . RarityType::fromId($enchantment->getRarity())->getColor() . $enchantment->getName() . " " . self::getRomanNumeral($enchantmentInstance->getLevel());
				}
			}
			if ($item->getNamedTag()->getTag(Item::TAG_DISPLAY)) $item->getNamedTag()->setTag("OriginalDisplayTag", $item->getNamedTag()->getTag(Item::TAG_DISPLAY)->safeClone());
			$lore = array_merge(explode("\n", $additionalInformation), $item->getLore());
			array_shift($lore);
			$item = $item->setLore($lore);
		}
		return TypeConverter::getInstance()->coreItemStackToNet($item);
	}

	public static function filter(ItemStack $itemStack): ItemStack {
		$item = TypeConverter::getInstance()->netItemStackToCore($itemStack);
		$tag = $item->getNamedTag();
		if (count($item->getEnchantments()) > 0) $tag->removeTag(Item::TAG_DISPLAY);

		if ($tag->getTag("OriginalDisplayTag") instanceof CompoundTag) {
			$tag->setTag(Item::TAG_DISPLAY, $tag->getTag("OriginalDisplayTag"));
			$tag->removeTag("OriginalDisplayTag");
		}
		$item->setNamedTag($tag);
		return TypeConverter::getInstance()->coreItemStackToNet($item);
	}
}