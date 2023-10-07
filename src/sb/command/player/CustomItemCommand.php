<?php

declare(strict_types=1);

namespace sb\command\player;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\command\CommandSender;
use sb\item\utils\BaitType;
use sb\item\CustomItems;
use sb\player\CorePlayer;

class CustomItemCommand extends BaseCommand {
	protected function prepare() : void{
		$this->setDescription("Custom Item command");
		$this->setPermission("pocketmine.command.me");
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		self::sendSetsMenu($sender);
	}

	public static function sendSetsMenu(CorePlayer $player) : void {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
		$menu->setName("§l§4All Custom Items");

		$menu->getInventory()->addItem(CustomItems::BANKNOTE()->getItem(1000));
		$menu->getInventory()->addItem(CustomItems::CRATEKEY()->getItem("Simple"));
		$menu->getInventory()->addItem(CustomItems::MONEYPOUCH()->getItem(1000, 10000));
		$menu->getInventory()->addItem(CustomItems::SELLWAND()->getItem(10));
		$menu->getInventory()->addItem(CustomItems::XPBOTTLE()->getItem(100));
		$menu->getInventory()->addItem(CustomItems::FISHING_BAIT()->getItem(BaitType::ARMOR()));
		//sets?
		$menu->setListener(function(InvMenuTransaction $transaction) {
			if(!$transaction->getPlayer()->hasPermission("customitem.command.use")) return $transaction->discard();
			else return $transaction->continue();
		});
		$menu->send($player);
	}
}