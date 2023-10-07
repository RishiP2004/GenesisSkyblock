<?php

declare(strict_types = 1);

namespace sb\command\player;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use sb\islands\IslandManager;
use sb\player\CorePlayer;
use sb\Skyblock;

use sb\player\traits\PlayerCallTrait;

use CortexPE\Commando\BaseCommand;

use pocketmine\command\CommandSender;

class SetHomeCommand extends BaseCommand {
	use PlayerCallTrait;

	public function prepare() : void {
		$this->setPermission("pocketmine.command.me");
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->registerArgument(0, new RawStringArgument("name", false));
	}

	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$this->getCoreUser($sender->getName(), function($user) use ($sender, $args) {
			if(is_null($user)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . $args["player"] . " is not a valid Player");
				return false;
			}
			if($user->getHome($args["name"]) !== null) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "The entered home name already exists.");
				return false;
			}
			$island = IslandManager::getInstance()->getIslandFromName($sender->getWorld()->getFolderName());

			if($island && !$island->isMember($sender->getCoreUser())) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You cannot set home in someone else's island.");
				return false;
			}
			if($sender->getKoth() !=null) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You cannot set home at a Koth.");
				return false;
			}
			if (!$sender->hasPermission("home." . count($user->getHomes()) + 1) and count($user->getHomes()) < 5) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You've reached the max amount of homes you can set");
				return false;
			} else {
				$user->createHome($args["name"], $sender->getPosition());
				$sender->sendMessage(Skyblock::PREFIX . $args["name"] . " set at your current Location");
			}
			return true;
		});
	}
}