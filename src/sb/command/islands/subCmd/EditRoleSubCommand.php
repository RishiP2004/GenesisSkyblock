<?php

namespace sb\command\islands\subCmd;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Toggle;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use sb\islands\Island;
use sb\islands\traits\IslandCallTrait;
use sb\islands\utils\IslandRole;
use sb\permission\utils\IslandPermissions;
use sb\player\CorePlayer;
use sb\Skyblock;
//todo: setting a default role
class EditRoleSubCommand extends BaseSubCommand {
	use IslandCallTrait;

	public function __construct() {
		parent::__construct(Skyblock::getInstance(), "editrole", "Edit a role for your island.");
		$this->setPermission("pocketmine.command.me");
	}

    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    /** @var CorePlayer $sender */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		$user = $sender->getCoreUser();

		if (!$user->hasIsland()) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have an island.");
			return;
		}
		$this->getIsland($user->getIsland(), function($island) use ($sender, $user) {
			if(is_null($island)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have an island");
				return false;
			}
			if(!$island->getRole($user)->hasPermission(IslandPermissions::PERMISSION_EDIT_ROLE)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have permission to do this.");
				return false;
			}
			$editableRoles = array_values(array_values(array_map(
				fn(IslandRole $role) => $role->getName(),
				array_filter($island->getRoles(), fn(IslandRole $role) => $role->getCanBeDeleted())
			)));
			$sender->sendForm(new CustomForm(
				"Edit a Role",
				[
					new Dropdown("role", "Role to edit.", $editableRoles)
				],
				function(Player $submitter, CustomFormResponse $response) use ($island, $editableRoles) : void {
					self::openRoleEditMenu($submitter, $editableRoles[$response->getInt("role")], $island);
				}
			));
			return true;
		});
    }

	public static function openRoleEditMenu(CorePlayer $player, string $role, Island $island) {
		$toggles = array_map(function (string $perm) use($player, $role, $island) {
			return new Toggle("$perm", ucwords(str_replace("_", " ", $perm)), $island->getRoleRaw($role)->hasPermission($perm));
		}, IslandPermissions::ALL_PERMISSIONS);

		$player->sendForm(new CustomForm(
			"Change permissions",
			$toggles,
			function(Player $submitter, CustomFormResponse $response) use ($island, $role) : void {
				foreach(IslandPermissions::ALL_PERMISSIONS as $k => $v) {
					$island->getRoleRaw($role)->setPermission($v, $response->getBool($v));
				}
				$submitter->sendMessage(Skyblock::PREFIX . "Successfully edited §c{$role}§7's permissions");
			}
		));
	}
}