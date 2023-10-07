<?php

namespace sb\command\islands\subCmd;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sb\islands\traits\IslandCallTrait;
use sb\Skyblock;

class TeleportSubCommand extends BaseSubCommand {
	use IslandCallTrait;

    public function __construct() {
        parent::__construct(Skyblock::getInstance(), "teleport", "Teleport to your island.");
        $this->setPermission("pocketmine.command.me");
    }

    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    /** @var Player $sender */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		$user = $sender->getCoreUser();

		if (!$user->hasIsland()) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have an island.");
			return;
		}
		$this->getIsland($user->getIsland(), function($island) use ($sender, $args) {
			if(is_null($island)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have an island.");
				return false;
			}
			$island->teleport($sender);
			$sender->sendMessage(TextFormat::colorize("&aYou have been warped to your island."));
			return true;
		});
    }

}