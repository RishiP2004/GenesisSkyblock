<?php

namespace sb\command\player\staff;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use sb\command\args\PlayerArgument;
use sb\item\CustomItems;
use sb\player\CorePlayer;
use sb\Skyblock;

class TicketCommand extends BaseCommand {
	public function prepare() : void {
		$this->setPermission("ticket.command");
		$this->registerArgument(0, new PlayerArgument("player", true));
	}

	public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$item = CustomItems::SLOTBOT_TICKET()->getItem();

		if(isset($args["player"])) {
			$args["player"]->getInventory()->canAddItem($item) ? $args["player"]->getInventory()->addItem($item) : $args["player"]->getWorld()->dropItem($args["player"]->getPosition()->asVector3(), $item);
			$args["player"]->sendMessage(Skyblock::PREFIX . $sender->getName() . " gave you a ticket.");
		} else {
			if(!$sender instanceof CorePlayer) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You must be a Player to use this Command");
				return;
			}
			$sender->getInventory()->canAddItem($item) ? $sender->getInventory()->addItem($item) : $sender->getWorld()->dropItem($sender->getPosition()->asVector3(), $item);
			$sender->sendMessage(Skyblock::PREFIX . "You have received a ticket.");
		}
	}
}