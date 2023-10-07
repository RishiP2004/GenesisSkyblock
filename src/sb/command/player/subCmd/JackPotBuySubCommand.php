<?php

declare(strict_types =1);

namespace sb\command\player\subCmd;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use CortexPE\Commando\args\IntegerArgument;
use sb\player\CorePlayer;
use sb\server\jackpot\JackpotHandler;
use sb\Skyblock;

class JackPotBuySubCommand extends BaseSubCommand {
	protected function prepare() : void {
		$this->setPermission("pocketmine.command.me");
		$this->registerArgument(0,new IntegerArgument("amount",true));
	}

	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		if(!isset($args["amount"])) {
			JackpotHandler::sendBuyForm($sender, 1000, 1);
			return;
		}
		if($args["amount"] <= 0) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "The amount must be greater then 0.");
			return;
		}
		JackpotHandler::sendBuyForm($sender, $args["amount"] * 1000, $args["amount"]);
	}
}