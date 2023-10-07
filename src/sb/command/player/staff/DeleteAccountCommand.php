<?php

declare(strict_types = 1);

namespace sb\command\player\staff;

use CortexPE\Commando\args\RawStringArgument;
use sb\Skyblock;

use sb\player\CorePlayer;

use sb\player\traits\PlayerCallTrait;

use CortexPE\Commando\BaseCommand;

use pocketmine\Server;

use pocketmine\command\CommandSender;

class DeleteAccountCommand extends BaseCommand {
	use PlayerCallTrait;

	public function prepare() : void {
		$this->setPermission("deleteaccount.command");
		$this->setAliases(["delacc"]);
		$this->registerArgument(0, new RawStringArgument("player", false));
	}

    public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$this->getCoreUser($args["player"], function($user) use ($sender, $args) {
			if(is_null($user)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . $args["player"] . " is not a valid Player");
				return false;
			} else {
				$user->unregister();
				unlink(Server::getInstance()->getDataPath() . "players/" . strtolower($user->getName()) . ".dat");
				
				$player = Server::getInstance()->getPlayerByPrefix($user->getName());
		
				if($player instanceof CorePlayer) {
					$player->kick($sender->getName() . " deleted your Account");
				}
				$sender->sendMessage(Skyblock::PREFIX . "Deleted " . $user->getName() . "'s Account");
				return true;
			}
        });
    }
}