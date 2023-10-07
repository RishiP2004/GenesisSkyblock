<?php

declare(strict_types = 1);

namespace sb\command\player\staff;

use CortexPE\Commando\args\RawStringArgument;
use sb\Skyblock;

use sb\player\CorePlayer;
use sb\player\traits\PlayerCallTrait;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\IntegerArgument;

use pocketmine\Server;

use pocketmine\command\CommandSender;

class ReduceMoneyCommand extends BaseCommand {
	use PlayerCallTrait;

	public function prepare() : void {
		$this->setPermission("reducemoney.command");
		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new IntegerArgument("amount"));
	}

	public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$this->getCoreUser($args["player"], function($user) use ($sender, $args) {
			if(is_null($user)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . $args["player"] . " is not a valid Player");
				return false;
			} else {
				$user->reduceMoney((int) $args["amount"]);

				$player = Server::getInstance()->getPlayerByPrefix($user->getName());
		
				if($player instanceof CorePlayer) {
					$player->sendMessage(Skyblock::PREFIX . $sender->getName() . " reduced your money by $" . $args["amount"]);
				}
				$sender->sendMessage(Skyblock::PREFIX . "Reduced " . $user->getName() . "'s money by $" . $args["amount"]);
				return true;
			}
		});
    }
}