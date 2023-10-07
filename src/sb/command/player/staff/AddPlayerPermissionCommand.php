<?php

declare(strict_types = 1);

namespace sb\command\player\staff;

use sb\Skyblock;

use sb\player\CorePlayer;

use sb\player\traits\PlayerCallTrait;
use sb\command\args\OfflinePlayerArgument;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;

use pocketmine\Server;

use pocketmine\command\CommandSender;

use pocketmine\permission\Permission;

class AddPlayerPermissionCommand extends BaseCommand {
	use PlayerCallTrait;

    public function prepare() : void {
    	$this->setPermission("addplayerpermission.command");
		$this->setAliases(["addpperm"]);
		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new RawStringArgument("permission"));
	}

    public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$this->getCoreUser($args["player"], function($user) use ($sender, $args) {
			if(is_null($user)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . $args["player"] . " is not a valid Player");
				return false;
			}
			if($user->hasPermission($args["permission"])) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . $user->getName() . " already has the Permission " . $args["permission"]);
				return false;
			} else {
				$perm = new Permission($args["permission"]);
				$user->addPermission($perm);

				$player = Server::getInstance()->getPlayerByPrefix($user->getName());
		
				if($player instanceof CorePlayer) {
					$player->sendMessage(Skyblock::PREFIX . $sender->getName() . " gave you the Permission " . $perm->getName());
				}
				$sender->sendMessage(Skyblock::PREFIX . "Added the Permission " . $perm->getName() . " to " . $user->getName());
				return true;
			}
        });
    }
}