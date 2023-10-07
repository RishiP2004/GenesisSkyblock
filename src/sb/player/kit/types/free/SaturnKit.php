<?php
namespace sb\player\kit\types\free;

use sb\item\CustomItems;
use sb\player\kit\Kit;
use sb\player\kit\utils\KitItemParser;
use sb\player\kit\utils\KitTier as Tier;
use pocketmine\utils\TextFormat as T;

class SaturnKit extends Kit{

	public function __construct(){
		parent::__construct("Saturn", "genesis.kit.saturn", 25000, Tier::PLAYER, KitItemParser::createItem("diamond_sword")
			->setCustomName("Saturn /kit")->setLore([]));
	}

	public function init(): void{
		$this->armour = [
			KitItemParser::createItem('diamond_helmet', [
				KitItemParser::createEnchantment('protection', 6), // Change 5 to 6
				KitItemParser::createEnchantment('unbreaking', 4), // Change 3 to 4
			])->setCustomName(T::colorize("&r&l&bSaturn Helmet")), // Change color to &r&l&b and rename

			KitItemParser::createItem('diamond_chestplate', [
				KitItemParser::createEnchantment('protection', 6), // Change 5 to 6
				KitItemParser::createEnchantment('unbreaking', 4), // Change 3 to 4
			])->setCustomName(T::colorize("&r&l&bSaturn Chestplate")), // Change color to &r&l&b and rename

			KitItemParser::createItem('diamond_leggings', [
				KitItemParser::createEnchantment('protection', 6), // Change 5 to 6
				KitItemParser::createEnchantment('unbreaking', 4), // Change 3 to 4
			])->setCustomName(T::colorize("&r&l&bSaturn Leggings")), // Change color to &r&l&b and rename

			KitItemParser::createItem('diamond_boots', [
				KitItemParser::createEnchantment('protection', 6), // Change 5 to 6
				KitItemParser::createEnchantment('unbreaking', 4), // Change 3 to 4
			])->setCustomName(T::colorize("&r&l&bSaturn Boots")), // Change color to &r&l&b and rename
		];

		$this->inventory = [
			KitItemParser::createItem("diamond_sword", [
				KitItemParser::createEnchantment('sharpness', 4), // Change 3 to 4
				KitItemParser::createEnchantment('unbreaking', 3), // Increase by 1
			])->setCustomName(T::colorize("&r&l&bSaturn Sword")), // Change color to &r&l&b and rename

			KitItemParser::createItem("diamond_pickaxe", [
				KitItemParser::createEnchantment('efficiency', 4), // Change 3 to 4
				KitItemParser::createEnchantment('unbreaking', 3), // Increase by 1
			])->setCustomName(T::colorize("&r&l&bSaturn Pickaxe")), // Change color to &r&l&b and rename

			KitItemParser::createItem("diamond_axe", [
				KitItemParser::createEnchantment('efficiency', 4), // Change 3 to 4
				KitItemParser::createEnchantment('unbreaking', 3), // Increase by 1
			])->setCustomName(T::colorize("&r&l&bSaturn Axe")), // Change color to &r&l&b and rename

			KitItemParser::createItem("diamond_shovel", [
				KitItemParser::createEnchantment('efficiency', 4), // Change 3 to 4
				KitItemParser::createEnchantment('unbreaking', 3), // Increase by 1
			])->setCustomName(T::colorize("&r&l&bSaturn Shovel")), // Change color to &r&l&b and rename

			CustomItems::XPBOTTLE()->getItem(4000), // Increase count
			KitItemParser::createItem("ender_pearl", [], 16),
			KitItemParser::createItem("cobblestone", [], 128),
			KitItemParser::createItem("cooked_beef", [], 48),
			KitItemParser::createItem("golden_apple", [], 24),
			KitItemParser::createItem("oak_wood", [], 48),
		];
	}
}
