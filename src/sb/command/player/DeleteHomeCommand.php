<?php

declare(strict_types = 1);

namespace sb\command\player;

use CortexPE\Commando\args\RawStringArgument;
use sb\Skyblock;

use sb\player\traits\PlayerCallTrait;

use CortexPE\Commando\BaseCommand;

use pocketmine\command\CommandSender;

class DeleteHomeCommand extends BaseCommand {
	use PlayerCallTrait;

	public function prepare() : void {
		$this->setPermission("pocketmine.command.me");
		$this->registerArgument(0, new RawStringArgument("name", false));
	}

	public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$this->getCoreUser($sender->getName(), function($user) use ($sender, $args) {
			if(is_null($user)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . $args["player"] . " is not a valid Player");
				return false;
			}
			if($user->getHome($args["name"]) === null){
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "The entered home name does not exist.");
				return false;
			} else {
				$user->deleteHome($args["name"]);
				$sender->sendMessage(Skyblock::PREFIX . $args[0] . " has been removed in your home list");
			}
			return true;
		});
	}
}