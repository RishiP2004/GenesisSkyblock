<?php

namespace sb\command\world\islandCache\subCmd;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use sb\Skyblock;
use sb\world\islandCache\IslandCacheHandler;

class SpawnSubCommand extends BaseSubCommand {
	public function __construct() {
		parent::__construct(Skyblock::getInstance(), "spawn", "Spawn Island Caches");
		$this->registerArgument(0, new IntegerArgument("amount"));
		$this->setPermission("islandcache.manage");
	}

	public function prepare() : void {
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		for($i = 1; $i == $args["amount"]; $i++) {
			IslandCacheHandler::spawn();
		}
		$sender->sendMessage(Skyblock::ERROR_PREFIX . "Spawned " . $args["amount"] . " Island Cache(s)");
	}
}