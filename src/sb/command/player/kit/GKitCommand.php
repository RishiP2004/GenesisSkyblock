<?php

declare(strict_types = 1);

namespace sb\command\player\kit;

use sb\player\CorePlayer;
use sb\player\kit\KitHandler;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;

class GKitCommand extends BaseCommand {
	public function prepare() : void {
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->setPermission("pocketmine.command.me");
	}
	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		KitHandler::sendGKitForm($sender);
	}
}