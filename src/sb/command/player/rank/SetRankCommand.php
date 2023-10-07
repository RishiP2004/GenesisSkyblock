<?php

declare(strict_types = 1);

namespace sb\command\player\rank;

use CortexPE\Commando\args\RawStringArgument;
use sb\Skyblock;

use sb\player\CorePlayer;
use sb\player\traits\PlayerCallTrait;
use sb\command\args\RankArgument;

use CortexPE\Commando\BaseCommand;

use pocketmine\Server;

use pocketmine\command\CommandSender;

class SetRankCommand extends BaseCommand {
	use PlayerCallTrait;

	public function prepare() : void {
		$this->setPermission("setrank.command");
		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new RankArgument("rank"));
	}

    public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$this->getCoreUser($args["player"], function($user) use ($sender, $args) {
			if(is_null($user)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . $args["player"] . " is not a valid Player");
				return false;
			}
			$user->setRank($args["rank"]);
			$player = Server::getInstance()->getPlayerExact($user->getName());

			if($player instanceof CorePlayer) {
				$player->setNameTag($player->getCoreUser()->getRank()->getNameTagFormatFor($player));
				$player->sendMessage(Skyblock::PREFIX . "Your rank was set to " . $args["rank"]->getColor() . $args["rank"]->getName());
			}
			$sender->sendMessage(Skyblock::PREFIX . "Set " . $user->getName() . "'s rank to " . $args["rank"]->getColor() . $args["rank"]->getName());
			return true;
		});
    }
}