<?php

namespace sb\command\islands\subCmd;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use sb\form\FormHandler;
use sb\permission\utils\IslandPermissions;
use sb\islands\utils\IslandRole;
use sb\islands\traits\IslandCallTrait;
use sb\player\CorePlayer;
use sb\Skyblock;
use sb\utils\PMUtils;

class CreateRoleSubCommand extends BaseSubCommand {
	use IslandCallTrait;

    public function __construct() {
        parent::__construct(Skyblock::getInstance(), "createrole", "Create a role for your island.");
		$this->setPermission("pocketmine.command.me");
    }

    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    /** @var CorePlayer $sender */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		$user = $sender->getCoreUser();

		if (!$user->hasIsland()) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "§r§c§l(!) §r§cYou do not have an island.");
			return;
		}
		$this->getIsland($user->getIsland(), function($island) use ($sender, $user) {
			if(is_null($island)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "§r§c§l(!) §r§cYou do not have an island");
				return false;
			}
			if (!$island->getRole($user)->hasPermission(IslandPermissions::PERMISSION_EDIT_ROLE)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have permission to do this.");
				return false;
			}
			$sender->sendForm(FormHandler::textInputForm(
				"Create a role",
				"Role Name",
				TextFormat::colorize(TextFormat::RED . "§r§c§l(!) §r§cRole names must be 1-12 characters, and only have real characters."),
				PMUtils::genValidation(1, 12),
				function(string $input) use($sender, $island) : void {
					if (!is_null($island->getRoleRaw($input))) {
						$sender->sendMessage(Skyblock::ERROR_PREFIX . "§r§c§l(!) §r§cThere is already a role with that name.");
						return;
					}
					$island->createRole($input, new IslandRole($input, []));
					$sender->sendMessage("§r§a§l(!) §r§aSuccessfully created the role {$input}");
					EditRoleSubCommand::openRoleEditMenu($sender, $input, $island);
				}
			));
			return true;
		});
    }

}