<?php

namespace sb\command\player\subCmd;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use sb\player\CorePlayer;
use sb\player\traits\PlayerCallTrait;
use sb\server\jackpot\JackpotHandler;
use sb\Skyblock;

class JackPotStatsSubCommand extends BaseSubCommand {
	use PlayerCallTrait;

	protected function prepare() : void {
		$this->setPermission("pocketmine.command.me");
		$this->registerArgument(0, new RawStringArgument("player"));
	}

	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if (!isset($args["player"])) {
			$sender->sendMessage("§r§d§lGenesis Jackpot Stats:");
			$sender->sendMessage("§r§bTotal Winnings: §r§d§l$" . "§r§d". number_format($sender->getCoreUser()->getJackPotEarnings(),2));
			$sender->sendMessage("§r§b§lTotal Tickets Purchased: §r§d" . JackpotHandler::getTickets($sender->getName()));
			$sender->sendMessage("§r§b§lTotal Jackpot Wins: §r§d" . $sender->getCoreUser()->getJackPotWins());
		} else {
			$this->getCoreUser($args["player"], function($user) use ($sender, $args) {
				if(is_null($user)) {
					$sender->sendMessage(Skyblock::ERROR_PREFIX . $args["player"] . " is not a valid Player");
					return false;
				} else {
					$sender->sendMessage("§r§d§lGenesis Jackpot Stats §r§7({$user->getName()})");
					$sender->sendMessage("§r§bTotal Winnings: §r§d§l$" . "§r§d". number_format($user->getJackPotEarnings(),2));
					$sender->sendMessage("§r§b§lTotal Tickets Purchased: §r§d" . JackpotHandler::getTickets($user->getName()));
					$sender->sendMessage("§r§b§lTotal Jackpot Wins: §r§d" . $user->getJackPotWins());
				}
				return true;
			});
		}
	}
}