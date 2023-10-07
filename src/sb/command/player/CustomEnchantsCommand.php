<?php

namespace sb\command\player;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use sb\inventory\CEInventory;
use sb\player\CorePlayer;

class CustomEnchantsCommand extends BaseCommand {
	protected function prepare() : void{
		$this->setDescription("Custom Enchants command");
		$this->setPermission("pocketmine.command.me");
		$this->setAliases(["ce", "ces", "customenchantments"]);
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		(new CEInventory($sender))->send($sender);
	}
}