<?php

namespace sb\command\world\koth\subCmd;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use sb\command\args\KothArgument;
use sb\Skyblock;

class StopSubCommand extends BaseSubCommand {
	public function __construct() {
		parent::__construct(Skyblock::getInstance(), "stop", "Stop a KoTH");
		$this->registerArgument(0, new KothArgument("koth"));
		$this->setPermission("koth.command.manage");
	}

	public function prepare() : void {
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		if(!$args["koth"]->isRunning()) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . $args["koth"]->getName() . " is not running");
			return;
		}
		$args["koth"]->stop();
		$sender->sendMessage(Skyblock::ERROR_PREFIX . $args["koth"]->getName() . " is now disabled");
	}
}