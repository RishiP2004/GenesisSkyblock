<?php
namespace sb\player\kit\utils;

use pocketmine\entity\effect\Effect;
use pocketmine\item\enchantment\ProtectionEnchantment;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\StringToEffectParser;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use sb\player\kit\KitException;

class KitItemParser{
	/**
	 * @param string $namespace
	 * @param array $enchantments
	 * @return Item
	 * @throws KitException
	 */
	public static function createItem(string $namespace, array $enchantments = [], $amount = 1): Item{
		$item = StringToItemParser::getInstance()->parse($namespace);
		if(!($item instanceof Item)) throw new KitException("Item $namespace not found for kit");


		foreach ($enchantments as $enchantment) {
			if($enchantment instanceof EnchantmentInstance) {
				$item->addEnchantment($enchantment);
			} else {
				throw new KitException("Enchantment $enchantment is not an instance of EnchantmentInstance");
			}
		}
		return $item->setCount($amount);
	}

	/**
	 * @param string $namespace
	 * @param int $level
	 * @return EnchantmentInstance
	 * @throws KitException
	 */
	public static function createEnchantment(string $namespace, int $level): EnchantmentInstance{
		$enchantment = StringToEnchantmentParser::getInstance()->parse($namespace);
		if(!($enchantment instanceof Enchantment)) throw new KitException("Enchantment $namespace not found for kit");

		if($level < 0) throw new KitException("Enchantment level must be greater than 0");

		return new EnchantmentInstance($enchantment, $level);
	}

	public static function createEffect(string $namespace, int $duration, int $amplifier): EffectInstance{
		$effect = StringToEffectParser::getInstance()->parse($namespace);
		if(!($effect instanceof Effect)) throw new KitException("Effect $namespace not found for kit");

		if($duration < 0) throw new KitException("Effect duration must be greater than 0");
		if($amplifier < 0) throw new KitException("Effect amplifier must be greater than 0");
		return new EffectInstance($effect, $duration, $amplifier);
	}


}