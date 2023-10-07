<?php

declare(strict_types=1);

namespace sb\command\player\coinflip;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use sb\command\player\coinflip\subCmd\CoinflipCancelSubCommand;
use sb\player\p2p\CoinflipHandler;
use sb\player\CorePlayer;
use sb\Skyblock;

class CoinflipCommand extends BaseCommand {
	protected function prepare() : void{
		$this->setDescription("Coinflip command");
		$this->setPermission("pocketmine.command.me");
		$this->registerArgument(0, new IntegerArgument("amount", true));
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->registerSubCommand(new CoinflipCancelSubCommand(Skyblock::getInstance(), "cancel"));
	}
	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		$amount = $args["amount"] ?? null;
		if($amount === null) {
			CoinflipHandler::sendMenu($sender);
			return;
		}
		if($amount < 1000) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "Amount must be greater than 1,000");
			return;
		}
		if($amount > 1000000000) {
			$sender->sendMessage(Skyblock::PREFIX . "Amount must be smaller than 1,000,000,000");
			return;
		}
		if(CoinflipHandler::get($sender) !== null) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You already have submitted a coin flip.");
			return;
		}
		CoinflipHandler::sendSelectColorMenu($sender, null, $amount);
	}
}