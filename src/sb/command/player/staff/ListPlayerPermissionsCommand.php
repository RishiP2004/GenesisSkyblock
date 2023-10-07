<?php

declare(strict_types = 1);

namespace sb\command\player\staff;

use CortexPE\Commando\args\RawStringArgument;
use sb\Skyblock;

use sb\player\traits\PlayerCallTrait;

use CortexPE\Commando\BaseCommand;

use pocketmine\command\CommandSender;

use pocketmine\utils\TextFormat;

class ListPlayerPermissionsCommand extends BaseCommand {
	use PlayerCallTrait;

	public function prepare() : void {
		$this->setPermission("listplayerpermissions.command");
		$this->registerArgument(0, new RawStringArgument("player", false));
	}

    public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$this->getCoreUser($args["player"], function($user) use ($sender, $args) {
			if(is_null($user)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . $args["player"] . " is not a valid Player");
				return false;
			} else {
				$sender->sendMessage(Skyblock::PREFIX . $user->getName() . "'s Permissions:");
				
				if(empty($user->getPermissions()) or !is_array($user->getPermissions())) {
					$sender->sendMessage(TextFormat::GRAY . "None");
					return true;
				}
				$sender->sendMessage(TextFormat::GRAY . implode(", ", (array) $user->getPermissions()));
				return true;
			}	
        });
    }
}