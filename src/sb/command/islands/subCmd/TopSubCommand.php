<?php

namespace sb\command\islands\subCmd;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sb\form\EasyFormButton;
use sb\form\FormHandler;
use sb\islands\Island;
use sb\islands\traits\IslandCallTrait;
use sb\player\CorePlayer;
use sb\Skyblock;
use sb\islands\IslandManager;
use sb\utils\PMUtils;

class TopSubCommand extends BaseSubCommand {

	public function __construct() {
		parent::__construct(Skyblock::getInstance(), "top", "See top islands");
		$this->setPermission("pocketmine.command.me");
	}

	public function prepare() : void {
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	/** @var CorePlayer $sender */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		IslandManager::sendTopIslandsMenu($sender);
	}
}