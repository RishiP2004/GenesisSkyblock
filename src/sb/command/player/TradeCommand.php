<?php

declare(strict_types=1);

namespace sb\command\player;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use sb\player\p2p\TradeInstance;
use sb\player\CorePlayer;
use sb\Skyblock;

class TradeCommand extends BaseCommand {
	private array $tradeCache = [];

	protected function prepare() : void{
		$this->setPermission("pocketmine.command.me");
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->registerArgument(0, new RawStringArgument("player"));
	}

	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		$partner = Server::getInstance()->getPlayerByPrefix($args["player"]);

		if(!$partner instanceof CorePlayer) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "No player named §c" . $args["player"] . "§7 was found");
			return;
		}
		$distance = $partner->getLocation()->maxPlainDistance($sender->getLocation()->asVector3());

		if($partner->getLocation()->getWorld()->getDisplayName() !== $sender->getLocation()->getWorld()->getDisplayName()) {
			$distance = 50;
		}
		if($partner->getName() === $sender->getName()) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You cannot trade yourself");
			return;
		}
		if($distance > 10) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "{$args["player"]} is not 10 blocks nearby you");
			return;
		}
		if(isset($this->tradeCache[strtolower($partner->getName())])) {
			$data = $this->tradeCache[strtolower($partner->getName())];

			if($data[0] === $sender->getName() && (time() - $data[1]) <= 30) {
				new TradeInstance($partner, $sender);
				return;
			}
		}
		$this->tradeCache[strtolower($sender->getName())] = [$partner->getName(), time()];

		$sender->sendMessage(Skyblock::PREFIX . "You have sent a trade request to §c" . $partner->getName());
		$partner->sendMessage(Skyblock::PREFIX . "You have received a trade request from §c" . $sender->getName());
	}
}