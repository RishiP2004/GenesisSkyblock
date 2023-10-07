<?php

declare(strict_types=1);

namespace sb\command\player;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;

use pocketmine\world\sound\ChestOpenSound;
use sb\inventory\KitCategoriesInventory;
use sb\player\CorePlayer;
use sb\player\kit\KitHandler;
use sb\Skyblock;
class KitCommand extends BaseCommand {
	private Skyblock $core;
	private string $name;

	public function __construct(Skyblock $core, string $name){
		parent::__construct($core, $name);

		$this->core = $core;
		$this->name = $name;
	}
	public function prepare() : void {
		$this->setPermission("pocketmine.command.me");
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$sender instanceof CorePlayer) return;

		$inventory = new KitCategoriesInventory($sender, KitHandler::getInstance()->getGroupedCategories());
		$inventory->send($sender);

		$sender->getWorld()->addSound($sender->getLocation(), new ChestOpenSound());
	}
}