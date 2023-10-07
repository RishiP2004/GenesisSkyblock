<?php

namespace sb\item;

use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\SingletonTrait;
use sb\item\utils\BaitType;

//todo: parse chanceable
class CustomItemParser {
	use SingletonTrait;
	/** @var CustomItem[] */
	private static array $items = [];

	public function isCustom(Item $item) : bool {
		return $item->getNamedTag()->getString(CustomItem::TAG_CUSTOM_ITEM, "") !== "";
	}

	public function parse(string $data): ?Item {
		$array = explode(':', $data);

		$name = array_shift($array);

		$cNames = [];

		foreach(CustomItems::getAll() as $nameC => $item) {
			$cNames[] = strtolower($nameC);
		}
		if(in_array(strtolower($name), $cNames)) {
			return $this->parseCustom($data);
		}
		//todo: remove damage?
		$damage = array_shift($array);

		try {
			$item = StringToItemParser::getInstance()->parse($name.':'.$damage) ?? LegacyStringToItemParser::getInstance()->parse($name.':'.$damage);
		} catch(LegacyStringToItemParserException) {
			return null;
		}

		if(!empty($array)){
			$count = array_shift($array);
			$item->setCount((int) $count);
		}
		if(!empty($array)) {
			$name = array_shift($array);

			if(strtolower($name) !== 'default') {
				$item->setCustomName($name);
			}
		}
		if(!empty($array)) {
			$enchantmentsArrays = array_chunk($array, 2);

			foreach ($enchantmentsArrays as $enchantmentsData){
				if(count($enchantmentsData) !== 2){
					continue;
				}

				$enchantment = StringToEnchantmentParser::getInstance()->parse($enchantmentsData[0]);

				if(!is_numeric($array[1])){
					continue;
				}
				$item->addEnchantment(new EnchantmentInstance($enchantment, (int) $enchantmentsData[1]));
			}
		}
		return $item;
	}
	/**
	 * In this case of custom items, we look at completely diff params.
	 * Eg. "banknote:amount:count" or "moneypouch:min:max:count"
	 *
	 * TODO: parsable weapon and armor sets and pets
	 */
	public function parseCustom(string $data) : ?Item {
		$array = explode(':', $data);
		$name = array_shift($array);

		switch($name) {
			case "banknote":
				return CustomItems::BANKNOTE()->getItem(array_shift($array))->setCount(array_shift($array) ?? 1);
			case "chunkcollector":
				return CustomItems::CHUNKCOLLECTOR()->getItem()->setCount(array_shift($array) ?? 1);
			case "cratekey":
				return CustomItems::CRATEKEY()->getItem(array_shift($array))->setCount(array_shift($array) ?? 1);
			case "moneypouch":
				return CustomItems::MONEYPOUCH()->getItem(array_shift($array), array_shift($array))->setCount(array_shift($array) ?? 1 );
			case "xpbottle":
				return CustomItems::XPBOTTLE()->getItem(array_shift($array))->setCount(array_shift($array) ?? 1);
			case "fishingbait":
				return CustomItems::FISHING_BAIT()->getItem(BaitType::fromString(array_shift($array)))->setCount(array_shift($array) ?? 1);
			case "sellwand":
				return CustomItems::SELLWAND()->getItem(array_shift($array) ?? 1)->setCount(array_shift($array) ?? 1);
			case "slotbotticket":
				return CustomItems::SLOTBOT_TICKET()->getItem()->setCount(array_shift($array) ?? 1);
			case "votecrate":
				return CustomItems::VOTECRATE()->getItem()->setCount(array_shift($array) ?? 1);
			case "permnote":
				return CustomItems::PERMNOTE()->getItem(array_shift($array))->setCount(array_shift($array) ?? 1 );
		}
	}
}