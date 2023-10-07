<?php

namespace sb\command\world\koth\subCmd;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use sb\Skyblock;
use sb\world\koth\KothHandler;

class ListSubCommand extends BaseSubCommand {
	public function __construct() {
		parent::__construct(Skyblock::getInstance(), "list", "List all KoTH");
		$this->setPermission("pocketmine.command.me");
	}

	protected function prepare() : void {
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		foreach(KothHandler::getAll() as $koth) {
			if($koth->isRunning()) $running = TextFormat::GREEN . TextFormat::BOLD . "RUNNING";
			else $running = TextFormat::RED . TextFormat::BOLD . "DISABLED";
			$sender->sendMessage(TextFormat::GOLD . $koth->getName() . ": " . $running);
		}
	}
}