<?php

namespace sb\command\islands\subCmd;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use sb\islands\traits\IslandCallTrait;
use sb\Skyblock;

class GoSubCommand extends BaseSubCommand {
	use IslandCallTrait;

    public function __construct() {
        parent::__construct(Skyblock::getInstance(), "go", "Go to your island quick.");
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
			$island->teleport($sender, true);
			$sender->sendMessage(Skyblock::PREFIX . "You have went to your island.");
			return true;
		});
    }

}