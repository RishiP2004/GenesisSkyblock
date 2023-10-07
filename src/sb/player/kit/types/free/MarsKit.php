<?php
namespace sb\player\kit\types\free;

use sb\item\CustomItems;
use sb\player\kit\Kit;
use sb\player\kit\utils\KitItemParser;
use sb\player\kit\utils\KitTier as Tier;
use pocketmine\utils\TextFormat as T;

class MarsKit extends Kit{

	public function __construct(){
		parent::__construct("Mars", "genesis.kit.mars", 16000, Tier::PLAYER, KitItemParser::createItem("diamond_sword")
			->setCustomName("Mars /kit")->setLore([]));
	}

	public function init(): void{
		$this->armour = [
			KitItemParser::createItem('diamond_helmet', [
				KitItemParser::createEnchantment('protection', 4), // Change 3 to 4
				KitItemParser::createEnchantment('unbreaking', 2),
			])->setCustomName(T::colorize("&r&6Mars Helmet")),

			KitItemParser::createItem('diamond_chestplate', [
				KitItemParser::createEnchantment('protection', 4), // Change 3 to 4
				KitItemParser::createEnchantment('unbreaking', 2),
			])->setCustomName(T::colorize("&r&6Mars Chestplate")),

			KitItemParser::createItem('diamond_leggings', [
				KitItemParser::createEnchantment('protection', 4), // Change 3 to 4
				KitItemParser::createEnchantment('unbreaking', 2),
			])->setCustomName(T::colorize("&r&6Mars Leggings")),

			KitItemParser::createItem('diamond_boots', [
				KitItemParser::createEnchantment('protection', 4), // Change 3 to 4
				KitItemParser::createEnchantment('unbreaking', 2),
			])->setCustomName(T::colorize("&r&6Mars Boots"))
		];

		$this->inventory = [
			KitItemParser::createItem("diamond_sword", [
				KitItemParser::createEnchantment('sharpness', 4), // Change 3 to 4
				KitItemParser::createEnchantment('unbreaking', 2),
			])->setCustomName(T::colorize("&r&6Mars Sword")),

			KitItemParser::createItem("diamond_pickaxe", [
				KitItemParser::createEnchantment('efficiency', 3),
				KitItemParser::createEnchantment('unbreaking', 2),
			])->setCustomName(T::colorize("&r&6Mars Pickaxe")),

			KitItemParser::createItem("diamond_axe", [
				KitItemParser::createEnchantment('efficiency', 3),
				KitItemParser::createEnchantment('unbreaking', 2),
			])->setCustomName(T::colorize("&r&6Mars Axe")),

			KitItemParser::createItem("diamond_shovel", [
				KitItemParser::createEnchantment('efficiency', 3),
				KitItemParser::createEnchantment('unbreaking', 2),
			])->setCustomName(T::colorize("&r&6Mars Shovel")),

			CustomItems::XPBOTTLE()->getItem(2000),
			KitItemParser::createItem("ender_pearl", [], 16),
			KitItemParser::createItem("cobblestone", [], 128),
			KitItemParser::createItem("cooked_beef", [], 48),
			KitItemParser::createItem("golden_apple", [], 24),
			KitItemParser::createItem("oak_wood", [], 48),
		];
	}
}
