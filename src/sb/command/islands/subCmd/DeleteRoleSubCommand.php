<?php

namespace sb\command\islands\subCmd;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use sb\permission\utils\IslandPermissions;
use sb\islands\utils\IslandRole;
use sb\islands\traits\IslandCallTrait;
use sb\player\CorePlayer;
use sb\Skyblock;

class DeleteRoleSubCommand extends BaseSubCommand {
	use IslandCallTrait;

	public function __construct() {
		parent::__construct(Skyblock::getInstance(), "deleterole", "Delete a role for your island.");
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
			$deletableRoles = array_values(array_values(array_map(
				fn(IslandRole $role) => $role->getName(),
				array_filter($island->getRoles(), fn(IslandRole $role) => $role->getCanBeDeleted() && $role->getName() !== $island->getDefaultRole())
			)));
			if (count($deletableRoles) < 1) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "There are no roles for you to delete.");
				return false;
			}
			$sender->sendForm(new CustomForm(
				"Delete a Role",
				[
					new Dropdown("role", "Role to delete.", $deletableRoles)
				],
				function(Player $submitter, CustomFormResponse $response) use ($deletableRoles, $island) : void {
					$island->deleteRole($role = $deletableRoles[$response->getInt("role")]);
					$submitter->sendMessage(Skyblock::PREFIX . "You have deleted the role: " . $role . ".");
				}
			));
			return true;
		});
    }

}