<?php

namespace sb\permission;

use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\PermissionManager;
use pocketmine\permission\PermissionParser;
use pocketmine\plugin\PluginDescription;
use pocketmine\utils\SingletonTrait;
use sb\Skyblock;

class CorePermissionManager {
	use SingletonTrait;

	public static function setup() : void {
		$staffPerms = [
			"accounts.command" => [
				"default" => "op",
				"description" => "Check all registered Accounts"
			],
			"addessence.command" => [
				"default" => "op",
				"description" => "Add Essence to a Player"
			],
			"addmobcoins.command" => [
				"default" => "op",
				"description" => "Add Mob coins to a Player"
			],
			"addmoney.command" => [
				"default" => "op",
				"description" => "Add money to a Player"
			],
			"addplayerpermission.command" => [
				"default" => "op",
				"description" => "Add a Permission to a Player"
			],
			"adduluru.command" => [
				"default" => "op",
				"description" => "Add Uluru to a Player"
			],
			"deleteaccount.command" => [
				"default" => "op",
				"description" => "Delete a player's account"
			],
			"listplayerpermissions.command" => [
				"default" => "op",
				"description" => "List all permissions a player has"
			],
			"reduceessence.command" => [
				"default" => "op",
				"description" => "Reduce a player's essence"
			],
			"reducemobcoins.command" => [
				"default" => "op",
				"description" => "Reduce a player's mob coins"
			],
			"reducemoney.command" => [
				"default" => "op",
				"description" => "Reduce a player's money"
			],
			"reduceuluru.command" => [
				"default" => "op",
				"description" => "Reduce a player's uluru"
			],
			"removeplayerpermission.command" => [
				"default" => "op",
				"description" => "Remove a Permission from a Player"
			],
			"setessence.command" => [
				"default" => "op",
				"description" => "Set a Player's essence"
			],
			"setmobcoins.command" => [
				"default" => "op",
				"description" => "Set a Player's mob coins"
			],
			"setmoney.command" => [
				"default" => "op",
				"description" => "Set a Player's money"
			],
			"setuluru.command" => [
				"default" => "op",
				"description" => "Set a Player's uluru"
			],
			"staffmode.command" => [
				"default" => "op",
				"description" => "Staffmode command"
			],
			"ticket.command" => [
				"default" => "op",
				"description" => "Give a slotbot ticket"
			],
			"topmoney.command" => [
				"default" => "op",
				"description" => "See top money"
			],
			"fly.command" => [
				"default" => "op",
				"description" => "Fly command"
			],
			"deletehome.command" => [
				"default" => "true",
				"description" => "Delete home command"
			],
			"home.1" => [
				"default" => "true",
				"description" => "Home perm"
			],
			"home.2" => [
				"default" => "op",
				"description" => "Home 2 perm"
			],
			"home.3" => [
				"default" => "op",
				"description" => "Home 3 perm"
			],
			"home.4" => [
				"default" => "op",
				"description" => "Home 4 perm"
			],
			"home.5" => [
				"default" => "op",
				"description" => "Home 5 perm"
			],
			"sets.command.use" => [
				"default" => "op",
				"description" => "Take sets from menu"
			],
			"customitem.command.use" => [
				"default" => "op",
				"description" => "Take custom items from menu"
			],
			"koth.command.manage" => [
				"default" => "op",
				"description" => "Manage a KoTH"
			],
			"key.command" => [
				"default" => "op",
				"description" => "Key command"
			],
			"islandcache.manage" => [
				"default" => "op",
				"description" => "Spawn Island cache(s)"
			],
			"setrank.command" => [
				"default" => "op",
				"description" => "Set a Player's rank"
			],
			"genesis.kit.member" => [
				"default" => "op",
				"description" => "access to member kit"
			],
		];
		self::register($staffPerms);
	}

	final static public function register(array $permissions) : void {
		$refClass = new \ReflectionClass(PluginDescription::class);
		$refProp = $refClass->getProperty("permissions");
		$refProp->setAccessible(true);

		$permissions = PermissionParser::loadPermissions($permissions);

		$desc = Skyblock::getInstance()->getDescription();
		$pluginPerms = $refProp->getValue($desc);
		$permManager = PermissionManager::getInstance();

		$opROOT = $permManager->getPermission(DefaultPermissions::ROOT_OPERATOR);
		$evROOT = $permManager->getPermission(DefaultPermissions::ROOT_OPERATOR);

		foreach($permissions as $default => $_permissions) {
			foreach($_permissions as $permission) {
				switch($default){
					case PermissionParser::DEFAULT_OP:
						$opROOT->addChild($permission->getName(), true);
						break;
					case PermissionParser::DEFAULT_NOT_OP:
						$evROOT->addChild($permission->getName(), true);
						$opROOT->addChild($permission->getName(), false);
						break;
					case PermissionParser::DEFAULT_TRUE:
						$evROOT->addChild($permission->getName(), true);
						break;
				}
				$pluginPerms[$default][] = $permission;
				$permManager->addPermission($permission);
			}
		}
		$refProp->setValue($desc, $pluginPerms);
	}
}