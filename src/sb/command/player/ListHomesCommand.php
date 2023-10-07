<?php

declare(strict_types = 1);

namespace sb\command\player;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use sb\player\traits\PlayerCallTrait;
use sb\Skyblock;

class ListHomesCommand extends BaseCommand {
	use PlayerCallTrait;

	public function prepare() : void {
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->setPermission("pocketmine.command.me");
	}

	public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$this->getCoreUser($sender->getName(), function($user) use ($sender, $args) {
			$sender->sendMessage(Skyblock::PREFIX . "Your current homes: " . implode(", ", $user->getHomes()));
		});
	}
}