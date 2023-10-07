<?php

declare(strict_types=1);

namespace sb\command\player;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use sb\inventory\WarpsInventory;
use sb\player\CorePlayer;
use sb\server\warps\WarpHandler;

class WarpCommand extends BaseCommand{
	protected function prepare(): void{
		$this->setDescription("Warp command");
		$this->setPermission("pocketmine.command.me");
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
		if(!$sender instanceof CorePlayer) return;
		$warps = WarpHandler::getInstance()->getWarps();

		$inventory = new WarpsInventory($warps);
		$inventory->send($sender);
	}
}