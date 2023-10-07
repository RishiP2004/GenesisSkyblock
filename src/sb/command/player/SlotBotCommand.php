<?php

namespace sb\command\player;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use sb\inventory\SlotBotMenu;
use sb\player\CorePlayer;

class SlotBotCommand extends BaseCommand {
	public function prepare() : void {
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->setPermission("pocketmine.command.me");
	}
	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		SlotBotMenu::send($sender);
	}
}