<?php

declare(strict_types = 1);

namespace sb\command\player;

use CortexPE\Commando\args\RawStringArgument;
use sb\Skyblock;

use sb\player\CorePlayer;
use sb\command\args\OfflinePlayerArgument;
use sb\player\traits\PlayerCallTrait;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\IntegerArgument;

use pocketmine\Server;

use pocketmine\command\CommandSender;

class PayCommand extends BaseCommand {
	use PlayerCallTrait;

	public function prepare() : void {
		$this->setPermission("pocketmine.command.me");
		$this->registerArgument(0, new RawStringArgument("player", false));
		$this->registerArgument(1, new IntegerArgument("amount", false));
	}

    public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$this->getCoreUser($args["player"], function($user) use($sender, $args) {
			if(is_null($user)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . $args["player"] . " is not a valid Player");
				return false;
			}
			if($sender->getCoreUser()->getMoney() < $args["amount"]) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have enough money");
				return false;
			} else {
				$user->setMoney($user->getCoins() + (int) $args["amount"]);
				$sender->getCoreUser()->setMoney($sender->getCoreUser()->getMoney() - $args["amount"]);

				$player = Server::getInstance()->getPlayerByPrefix($user->getName());
		
				if($player instanceof CorePlayer) {
					$player->sendMessage(Skyblock::PREFIX . $sender->getName() . " paid you $" . $args["amount"]);
				}
				$sender->sendMessage(Skyblock::PREFIX . "Paid " . $user->getName() . " $" . $args["amount"]);
				return true;
			}
        });
    }
}