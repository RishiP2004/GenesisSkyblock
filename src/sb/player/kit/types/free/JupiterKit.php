<?php
namespace sb\player\kit\types\free;

use sb\item\CustomItems;
use sb\player\kit\Kit;
use sb\player\kit\utils\KitItemParser;
use sb\player\kit\utils\KitTier as Tier;
use pocketmine\utils\TextFormat as T;

class JupiterKit extends Kit{

	public function __construct(){
		parent::__construct("Jupiter", "genesis.kit.jupiter", 20000, Tier::PLAYER, KitItemParser::createItem("diamond_sword")
			->setCustomName("Jupiter /kit")->setLore([]));
	}

	public function init(): void{
		$this->armour = [
			KitItemParser::createItem('diamond_helmet', [
				KitItemParser::createEnchantment('protection', 5), // Change 4 to 5
				KitItemParser::createEnchantment('unbreaking', 3), // Increase by 1
			])->setCustomName(T::colorize("&r&d&lJupiter Helmet")), // Change color to &r&d&l and rename

			KitItemParser::createItem('diamond_chestplate', [
				KitItemParser::createEnchantment('protection', 5), // Change 4 to 5
				KitItemParser::createEnchantment('unbreaking', 3), // Increase by 1
			])->setCustomName(T::colorize("&r&d&lJupiter Chestplate")), // Change color to &r&d&l and rename

			KitItemParser::createItem('diamond_leggings', [
				KitItemParser::createEnchantment('protection', 5), // Change 4 to 5
				KitItemParser::createEnchantment('unbreaking', 3), // Increase by 1
			])->setCustomName(T::colorize("&r&d&lJupiter Leggings")), // Change color to &r&d&l and rename

			KitItemParser::createItem('diamond_boots', [
				KitItemParser::createEnchantment('protection', 5), // Change 4 to 5
				KitItemParser::createEnchantment('unbreaking', 3), // Increase by 1
			])->setCustomName(T::colorize("&r&d&lJupiter Boots")), // Change color to &r&d&l and rename
		];

		$this->inventory = [
			KitItemParser::createItem("diamond_sword", [
				KitItemParser::createEnchantment('sharpness', 4), // Change 3 to 4
				KitItemParser::createEnchantment('unbreaking', 3), // Increase by 1
			])->setCustomName(T::colorize("&r&d&lJupiter Sword")), // Change color to &r&d&l and rename

			KitItemParser::createItem("diamond_pickaxe", [
				KitItemParser::createEnchantment('efficiency', 4), // Change 3 to 4
				KitItemParser::createEnchantment('unbreaking', 3), // Increase by 1
			])->setCustomName(T::colorize("&r&d&lJupiter Pickaxe")), // Change color to &r&d&l and rename

			KitItemParser::createItem("diamond_axe", [
				KitItemParser::createEnchantment('efficiency', 4), // Change 3 to 4
				KitItemParser::createEnchantment('unbreaking', 3), // Increase by 1
			])->setCustomName(T::colorize("&r&d&lJupiter Axe")), // Change color to &r&d&l and rename

			KitItemParser::createItem("diamond_shovel", [
				KitItemParser::createEnchantment('efficiency', 4), // Change 3 to 4
				KitItemParser::createEnchantment('unbreaking', 3), // Increase by 1
			])->setCustomName(T::colorize("&r&d&lJupiter Shovel")), // Change color to &r&d&l and rename

			CustomItems::XPBOTTLE()->getItem(3000), // Increase count
			KitItemParser::createItem("ender_pearl", [], 16),
			KitItemParser::createItem("cobblestone", [], 128),
			KitItemParser::createItem("cooked_beef", [], 48),
			KitItemParser::createItem("golden_apple", [], 24),
			KitItemParser::createItem("oak_wood", [], 48),
		];
	}
}
