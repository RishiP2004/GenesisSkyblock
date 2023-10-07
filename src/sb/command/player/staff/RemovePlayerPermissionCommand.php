<?php

declare(strict_types = 1);

namespace sb\command\player\staff;

use sb\Skyblock;

use sb\player\CorePlayer;
use sb\player\traits\PlayerCallTrait;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;

use pocketmine\Server;

use pocketmine\command\CommandSender;

use pocketmine\permission\Permission;

class RemovePlayerPermissionCommand extends BaseCommand {
	use PlayerCallTrait;

	public function prepare() : void {
		$this->setPermission("removeplayerpermission.command");
		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new RawStringArgument("permission"));
	}

    public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$this->getCoreUser($args["player"], function($user) use ($sender, $args) {
			if(is_null($user)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . $args["player"] . " is not a valid Player");
				return false;
			}
			if(!$user->hasPermission($args["permission"]) && strtolower($args["permission"]) !== "all") {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . $user->getName() . " doesn't have the Permission " . $args["permission"]);
				return false;
			} else {
				if(strtolower($args["permission"]) === "all") {
					$user->setPermissions([]);
					$sender->sendMessage(Skyblock::PREFIX . "Removed all Permissions from " . $user->getName());
					return true;
				}
				$perm = new Permission($args["permission"]);
				
				$user->removePermission($perm);

				$player = Server::getInstance()->getPlayerByPrefix($user->getName());
		
				if($player instanceof CorePlayer) {
					$player->sendMessage(Skyblock::PREFIX . $sender->getName() . " Removed the Permission " . $perm->getName() . " from you");
				}
				$sender->sendMessage(Skyblock::PREFIX . "Removed the Permission " . $perm->getName() . " from " . $user->getName());
				return true;
			}
        });
    }
}