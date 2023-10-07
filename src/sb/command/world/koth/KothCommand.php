<?php

declare(strict_types=1);

namespace sb\command\world\koth;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;

use sb\command\world\koth\subCmd\ListSubCommand;
use sb\command\world\koth\subCmd\StartSubCommand;
use sb\command\world\koth\subCmd\StopSubCommand;

class KothCommand extends BaseCommand {
	public function prepare(): void {
		$this->setPermission("pocketmine.command.me");
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->registerSubCommand(new ListSubCommand());
		$this->registerSubCommand(new StartSubCommand());
		$this->registerSubCommand(new StopSubCommand());
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		//todo: change to menu to show rewards
		$sender->sendMessage($this->getUsageMessage());
	}
}