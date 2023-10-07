<?php

namespace sb\command\player\coinflip\subCmd;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use sb\player\p2p\CoinflipHandler;
use sb\player\CorePlayer;
use sb\Skyblock;

class CoinflipCancelSubCommand extends BaseSubCommand {
	protected function prepare() : void {
		$this->setPermission("pocketmine.command.me");
	}

	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		if(CoinflipHandler::get($sender) == null) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have a coinflip");
			return;
		}
		CoinflipHandler::remove($sender);
	}
}