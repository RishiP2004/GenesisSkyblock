<?php

namespace sb\command\player\staff;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;

use pocketmine\command\CommandSender;

class StaffModeCommand extends BaseCommand {
	public function prepare() : void {
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->setPermission("staffmode.command");
	}

	public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$sender->setStaffMode($sender->isInStaffMode() ? false : true);
	}
}