<?php

declare(strict_types = 1);

namespace sb\command\player;

use sb\command\args\PlayerArgument;
use sb\player\CorePlayer;

use CortexPE\Commando\BaseCommand;

use pocketmine\command\CommandSender;
use sb\Skyblock;

class PingCommand extends BaseCommand {
	public function prepare() : void {
		$this->setPermission("pocketmine.command.me");
		$this->registerArgument(0, new PlayerArgument("player", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		if(isset($args["player"])) {
			if(!$sender->hasPermission($this->getPermission() . ".other")) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have Permission to use this Command");
				return;
			}
			$sender->sendMessage(Skyblock::PREFIX . $args["player"]->getName() . "'s PingCommand is: " . $args["player"]->getNetworkSession()->getPing());
			return;
		}
		if(!$sender instanceof CorePlayer) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You must be a Player to use this Command");
			return;
		}
		$sender->sendMessage(Skyblock::PREFIX. "Your Ping is: " . $sender->getNetworkSession()->getPing());
	}
}