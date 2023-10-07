<?php

declare(strict_types = 1);

namespace sb\command\player\rank;

use CortexPE\Commando\args\RawStringArgument;
use sb\Skyblock;

use sb\player\CorePlayer;
use sb\player\traits\PlayerCallTrait;

use CortexPE\Commando\BaseCommand;

use pocketmine\command\CommandSender;

class RankCommand extends BaseCommand {
	use PlayerCallTrait;

	public function prepare() : void {
		$this->setPermission("pocketmine.command.me");
		$this->registerArgument(0, new RawStringArgument("player", true));
	}
    
    public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
        if(isset($args["player"])) {
			$this->getCoreUser($args["player"], function($user) use ($sender, $args) {
				if(is_null($user)) {
					$sender->sendMessage(Skyblock::ERROR_PREFIX . $args["player"] . " is not a valid Player");
					return false;
				} else {
					$sender->sendMessage(Skyblock::PREFIX . $user->getName() . "'s Rank: " . $user->getRank()->getColor() . $user->getRank()->getName());
					return true;
				}
			});
			return;
        }
		if(!$sender instanceof CorePlayer) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You must be a Player to use this Command");
			return;
		}
		$sender->sendMessage(Skyblock::PREFIX . "Your Rank: " . $sender->getCoreUser()->getRank()->getColor() . $sender->getCoreUser()->getRank()->getName());
    }
}