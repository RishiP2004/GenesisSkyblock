<?php
namespace sb\player\kit\types\free;

use sb\item\CustomItems;
use sb\player\kit\Kit;
use sb\player\kit\utils\KitItemParser;
use sb\player\kit\utils\KitTier as Tier;
use pocketmine\utils\TextFormat as T;

class MercuryKit extends Kit{

	public function __construct(){
		parent::__construct("Mercury", "genesis.kit.mercury", 12000, Tier::PLAYER, KitItemParser::createItem("diamond_sword")
			->setCustomName("Mercury /kit")->setLore([]));
	}

	public function init(): void{
		$this->armour = [
			KitItemParser::createItem('diamond_helmet', [
				KitItemParser::createEnchantment('protection', 2),
				KitItemParser::createEnchantment('unbreaking', 1),

			])->setCustomName(T::colorize("&r&3Mercury Helmet")),

			KitItemParser::createItem('diamond_chestplate', [
				KitItemParser::createEnchantment('protection',2),
				KitItemParser::createEnchantment('unbreaking',1),

			])->setCustomName(T::colorize("&r&3Mercury Chestplate")),

			KitItemParser::createItem('diamond_leggings', [
				KitItemParser::createEnchantment('protection', 2),
				KitItemParser::createEnchantment('unbreaking', 1),

			])->setCustomName(T::colorize("&r&3Mercury Leggings")),

			KitItemParser::createItem('diamond_boots', [
				KitItemParser::createEnchantment('protection',2),
				KitItemParser::createEnchantment('unbreaking', 1),

			])->setCustomName(T::colorize("&r&3Mercury Boots"))
		];

		$this->inventory = [
			KitItemParser::createItem("diamond_sword", [
				KitItemParser::createEnchantment('sharpness', 2),
				KitItemParser::createEnchantment('unbreaking', 1),

			])->setCustomName(T::colorize("&r&3Mercury Sword")),

			KitItemParser::createItem("diamond_pickaxe", [
				KitItemParser::createEnchantment('efficiency', 2),
				KitItemParser::createEnchantment('unbreaking', 1),

			])->setCustomName(T::colorize("&r&3Mercury Pickaxe")),
			KitItemParser::createItem("diamond_axe", [
				KitItemParser::createEnchantment('efficiency',2),
				KitItemParser::createEnchantment('unbreaking',1),

			])->setCustomName(T::colorize("&r&3Mercury Axe")),
			KitItemParser::createItem("diamond_shovel", [
				KitItemParser::createEnchantment('efficiency', 2),
				KitItemParser::createEnchantment('unbreaking',1),

			])->setCustomName(T::colorize("&r&3Mercury Shovel")),

			CustomItems::XPBOTTLE()->getItem(1000),
			KitItemParser::createItem("ender_pearl", [], 6),
			KitItemParser::createItem("cobblestone", [], 64),
			KitItemParser::createItem("cooked_beef", [], 32),
			KitItemParser::createItem("golden_apple", [], 6),
			KitItemParser::createItem("oak_wood", [], 32),
		];
	}
}