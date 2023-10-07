<?php

namespace sb\command\islands\subCmd;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sb\form\EasyFormButton;
use sb\form\FormHandler;
use sb\islands\Island;
use sb\islands\traits\IslandCallTrait;
use sb\player\CorePlayer;
use sb\Skyblock;

//todo
class JoinSubCommand extends BaseSubCommand {
	use IslandCallTrait;

	public function __construct() {
		parent::__construct(Skyblock::getInstance(), "join", "join island invitations");
		$this->setPermission("pocketmine.command.me");
	}

	public function prepare() : void {
		$this->registerArgument(0, new RawStringArgument("name", false));
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	/** @var Player $sender */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		$user = $sender->getCoreUser();
		/**@var CorePlayer $sender */
		if ($user->hasIsland()) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You already have an island.");
			return;
		}
		$islandInvites = $sender->getIslandInvites();

		if(!isset($islandInvites) or count($islandInvites) == 0) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You have no island invitations.");
			return;
		}

		if($sender->getIslandName() !== "") {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You already have an island.");
			return;
		}
		$name = $args["name"] ?? null;
		if($name === null) return;

		$invite = $islandInvites[$name] ?? null;

		if($invite === null) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You have no island invitations from that island.");
			return;
		}

		$this->getIsland($invite, function(Island $island) use ($sender, $user) {
			if(is_null($island)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You have no island invitations from that island.");
				return false;
			}
			$user->setIsland($island->getName());
			$sender->setIslandName($island->getName());
			$island->addMember($sender);
			$island->announce("&r&a&l(!) &r&a{$sender->getName()} has joined your island.");
			$island->teleport($sender);
			return true;
		});
	}
}
