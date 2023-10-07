<?php

namespace sb\command\islands\subCmd;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sb\islands\traits\IslandCallTrait;
use sb\Skyblock;
use sb\form\EasyFormButton;
use sb\form\FormHandler;
use sb\islands\IslandManager;

class WarpSubCommand extends BaseSubCommand {
	use IslandCallTrait;

    public function __construct() {
        parent::__construct(Skyblock::getInstance(), "warp", "Warp to someone else's island.");
       $this->setPermission("pocketmine.command.me");
    }

    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $sender->sendForm(FormHandler::easyForm(
            "Island Warp",
            "Choose a island to warp to!",
            [
                ...array_map(fn(array $data) => new EasyFormButton(
                    $data[0] . "\nOwner: " . $data[2],
                    null,
                    true,
                    function(Player $submitter) use ($data) : void {
						$this->getIsland($data[1], function($island) use ($submitter, $data) {
							if (is_null($island)) {
								$submitter->sendMessage(TextFormat::colorize("&cThis island no longer exists."));
								return;
							}
							if ($island->isLocked()) {
								$submitter->sendMessage(TextFormat::colorize("&cThis island is now locked."));
								return;
							}
							if (!$island->isWorldLoaded()) {
								$submitter->sendMessage(TextFormat::colorize("&cThis island is not loaded, and can only be loaded by island members."));
								return;
							}
							$submitter->sendMessage(TextFormat::colorize("&aYou have been teleported to " . $data[2] . "'s island!"));
							$island->teleport($submitter);
						});
                    }),
                    IslandManager::getInstance()->retrieveWarpableIslands()
                )
            ],
        ));
    }
}