<?php

namespace sb\command\islands\subCmd;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sb\permission\utils\IslandPermissions;
use sb\islands\traits\IslandCallTrait;
use sb\Skyblock;

class LockSubCommand extends BaseSubCommand {
	use IslandCallTrait;

    public function __construct() {
        parent::__construct(Skyblock::getInstance(), "lock", "Lock your island.");
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
		$this->getIsland($user->getIsland(), function($island) use ($sender, $user, $args) {
			if(is_null($island)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have an island.");
				return false;
			}
			if (!$island->getRole($user)->hasPermission(IslandPermissions::PERMISSION_LOCK)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have permission to do this.");
				return false;
			}
			$island->setLocked(!$island->isLocked());
			$sender->sendMessage(TextFormat::colorize($island->isLocked() ? "&cThe island is now locked." : "&aThe island is no longer locked."));
			return true;
		});
    }
}