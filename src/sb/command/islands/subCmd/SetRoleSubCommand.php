<?php

namespace sb\command\islands\subCmd;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use sb\islands\utils\IslandMember;
use sb\permission\utils\IslandPermissions;
use sb\islands\utils\IslandRole;
use sb\islands\traits\IslandCallTrait;
use sb\Skyblock;

class SetRoleSubCommand extends BaseSubCommand {
	use IslandCallTrait;

    public function __construct() {
		parent::__construct(Skyblock::getInstance(), "setrole", "Set a member's role");
		$this->setPermission("pocketmine.command.me");
    }

    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    /** @var Player $sender */
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
			if(!$island->getRole($user)->hasPermission(IslandPermissions::PERMISSION_SET_ROLE)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have permission to do this.");
				return false;
			}
			$assignableRoles = array_values(array_values(array_map(
				fn(IslandRole $role) => $role->getName(),
				array_filter($island->getRoles(), fn(IslandRole $role) => $role->getName() !== "Owner")
			)));
			$assignableMembers = array_values(array_values(array_map(
				fn(IslandMember $member) => $member->getName(),
				array_filter($island->getMembers(), fn(IslandMember $member) =>
					$member->getName() !== $island->getOwner() &&
					!$island->getRole($member->getRole())->hasPermission(IslandPermissions::PERMISSION_SET_ROLE)
				)
			)));
			if (count($assignableMembers) < 1) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "There are no members to set a role to.");
				return false;
			}
			$sender->sendForm(new CustomForm(
				"Set a Member's Role",
				[
					new Dropdown("member", "Member to assign role to.", $assignableMembers),
					new Dropdown("role", "Role to assign.", $assignableRoles)
				],
				function(Player $submitter, CustomFormResponse $response) use($assignableMembers, $assignableRoles, $island) : void {
					$island->getMember($name = $assignableMembers[$response->getInt("member")])->setRole($role = $assignableRoles[$response->getInt("role")]);
					$submitter->sendMessage(Skyblock::PREFIX . "You have set " . $name . "'s role to " . $role . ".");
				}
			));
			return true;
		});
    }

}