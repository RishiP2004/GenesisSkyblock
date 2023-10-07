<?php
namespace sb\player\kit\types\free;

use sb\item\CustomItems;
use sb\player\kit\Kit;
use sb\player\kit\utils\KitItemParser;
use sb\player\kit\utils\KitTier as Tier;
use pocketmine\utils\TextFormat as T;

class MemberKit extends Kit{

	public function __construct(){
		parent::__construct("Member", "genesis.kit.member", 9600, Tier::PLAYER, KitItemParser::createItem("iron_helmet")
			->setCustomName("Member /kit")->setLore([]));
	}

	public function init(): void{
		$this->armour = [
			KitItemParser::createItem('iron_helmet', [
				KitItemParser::createEnchantment('protection', 1),
				KitItemParser::createEnchantment('unbreaking', 1),

			])->setCustomName(T::colorize("&r&aMember Helmet"))
			->setLore([
				"",
				T::colorize("&r&7A member's first set of armour"),
			]),
			KitItemParser::createItem('iron_chestplate', [
				KitItemParser::createEnchantment('protection',1),
				KitItemParser::createEnchantment('unbreaking',1),

			])->setCustomName(T::colorize("&r&aMember Chestplate"))
			->setLore([
				"",
				T::colorize("&r&7A member's first set of armour"),
			]),
			KitItemParser::createItem('iron_leggings', [
				KitItemParser::createEnchantment('protection', 1),
				KitItemParser::createEnchantment('unbreaking', 1),

			])->setCustomName(T::colorize("&r&aMember Leggings"))

			->setLore([
				"",
				T::colorize("&r&7A member's first set of armour"),
			]),

			KitItemParser::createItem('iron_boots', [
				KitItemParser::createEnchantment('protection',1),
				KitItemParser::createEnchantment('unbreaking', 1),

			])->setCustomName(T::colorize("&r&aMember Boots"))
			->setLore([
				"",
				T::colorize("&r&7A member's first set of armour"),
			]),
		];

		$this->inventory = [
			KitItemParser::createItem("iron_sword", [
				KitItemParser::createEnchantment('sharpness', 1),
				KitItemParser::createEnchantment('unbreaking', 1),

			])->setCustomName(T::colorize("&r&aMember Sword"))
			->setLore([
				"",
				T::colorize("&r&7A member's first sword"),
			]),
			KitItemParser::createItem("iron_pickaxe", [
				KitItemParser::createEnchantment('efficiency', 1),
				KitItemParser::createEnchantment('unbreaking', 1),

			])->setCustomName(T::colorize("&r&aMember Pickaxe"))
			->setLore([
				"",
				T::colorize("&r&7A member's first pickaxe"),
			]),

			KitItemParser::createItem("iron_axe", [
				KitItemParser::createEnchantment('efficiency',1),
				KitItemParser::createEnchantment('unbreaking',1),

			])->setCustomName(T::colorize("&r&aMember Axe"))

			->setLore([
				"",
				T::colorize("&r&7A member's first axe"),
			]),

			KitItemParser::createItem("iron_shovel", [
				KitItemParser::createEnchantment('efficiency', 1),
				KitItemParser::createEnchantment('unbreaking',1),

			])->setCustomName(T::colorize("&r&aMember Shovel"))

			->setLore([
				"",
				T::colorize("&r&7A member's first shovel"),
			]),
			CustomItems::XPBOTTLE()->getItem(500),
			KitItemParser::createItem("crafting_table", []),
			KitItemParser::createItem("furnace", []),
			KitItemParser::createItem("chest", []),
			KitItemParser::createItem("ender_pearl", [], 5),
			KitItemParser::createItem("cobblestone", [], 32),
			KitItemParser::createItem("cooked_beef", [], 16),
			KitItemParser::createItem("golden_apple", [], 1),
			KitItemParser::createItem("oak_wood", [], 16),
			KitItemParser::createItem("wheat_seeds", [], 16),
			KitItemParser::createItem("sugar_cane", [], 16),
			KitItemParser::createItem("melon_seeds", [], 16),
			KitItemParser::createItem("pumpkin_seeds", [], 16),
		];
	}
}