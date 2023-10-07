<?php

declare(strict_types=1);

namespace sb\command\world\stronghold;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;

class StrongholdCommand extends BaseCommand {
	public function prepare(): void {
		$this->setPermission("stronghold.command");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		//todo: show menu to show rewards and stuff
	}
}