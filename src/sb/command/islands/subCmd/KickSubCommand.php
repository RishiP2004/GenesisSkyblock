<?php

namespace sb\command\islands\subCmd;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use sb\form\EasyFormButton;
use sb\form\FormHandler;
use sb\islands\Island;
use sb\islands\utils\IslandMember;
use sb\permission\utils\IslandPermissions;
use sb\islands\traits\IslandCallTrait;
use sb\player\CorePlayer;
use sb\Skyblock;

class KickSubCommand extends BaseSubCommand {
	use IslandCallTrait;

    public function __construct() {
        parent::__construct(Skyblock::getInstance(), "kick", "Kick a member from your island.");
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
		$this->getIsland($user->getIsland(), function(Island $island) use ($sender, $user) {
			if(is_null($island)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have an island");
				return false;
			}
			if(!$island->getRole($user)->hasPermission(IslandPermissions::PERMISSION_KICK)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have permission to do this.");
				return false;
			}
			$kickableMembers = array_values(array_values(array_filter(
				array_values($island->getMembers()),
				function(IslandMember $member) use($island) : bool {
					return !$island->getRoleRaw($member->getRole())->hasPermission(IslandPermissions::PERMISSION_KICK) && $member->getName() !== $island->getLeader();
				}
			)));
			if (count($kickableMembers) < 1) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "There are no members for you to kick.");
				return false;
			}
			$sender->sendForm(FormHandler::easyForm(
				"Kick Member",
				"Choose a member to kick from your island.",
				[
					...array_map(
						fn(IslandMember $member) => new EasyFormButton(
							$member->getName() . "\nRole: " . $member->getRole(),
							null,
							true,
							function(Player $submitter) use ($member, $island) : void {
								$island->removeMember($member);

								if($member->getPlayer()?->isOnline()) {

									/** @var CorePlayer $player */
									$player = $member->getPlayer();
									$player->getCoreUser()->setIsland("");
									$player->setIslandName("");
								}

								$island->announce("&r&c&l(!) &r&c{$member->getName()} has been kicked from your island.");
								$submitter->sendMessage(TextFormat::colorize("&r&a&l(!) &r&aYou have kicked {$member->getName()} from your island successfully."));

								$p = Server::getInstance()->getPlayerByPrefix($member->getName());
								if($p instanceof CorePlayer) {
									$p->getCoreUser()->setIsland("");
									$p->setIslandName("");
									$p->sendMessage(TextFormat::colorize("&r&c&l(!) &r&cYou have been kicked from your island."));
								}
							}
						),
						$kickableMembers
					)
				]
			));
			return true;
		});
    }

}