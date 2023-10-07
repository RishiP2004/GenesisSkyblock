<?php

namespace sb\command\islands\subCmd;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sb\islands\traits\IslandCallTrait;
use sb\player\CorePlayer;
use sb\Skyblock;

class LeaveSubCommand extends BaseSubCommand {
	use IslandCallTrait;

	public function __construct() {
		parent::__construct(Skyblock::getInstance(), "leave", "Leave your island.");
		$this->setPermission("pocketmine.command.me");
	}

	public function prepare() : void {
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	/** @var CorePlayer $sender */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		$user = $sender->getCoreUser();

		if (!$user->hasIsland()) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have an island.");
			return;
		}
		$this->getIsland($user->getIsland(), function($island) use ($sender, $args, $user) {
			if(is_null($island)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have an island.");
				return false;
			}
			if($island->getRole($user)->getName() != "Owner") {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You cannot be island leader to leave it. Either transfer owner, or disband it.");
				return false;
			}
			$this->sendIslandLeave($sender);
			return true;
		});
	}

	public function sendIslandLeave(Player $player) {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_HOPPER);
		$menu->setName("Are you sure?");
		$menu->getInventory()->setContents([
			1 => VanillaBlocks::GLAZED_TERRACOTTA()->asItem()->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Yes! ")->setLore([
				TextFormat::RESET . " ",
				TextFormat::RESET . TextFormat::GRAY . "If you leave, You must be invited back into the island.",
				TextFormat::RESET . " ",
				TextFormat::RESET . TextFormat::WHITE . "If you do not want to leave your island",
				TextFormat::RESET . TextFormat::WHITE . "click the " . TextFormat::RED . "No " . TextFormat::WHITE . "button or close this menu!",
				TextFormat::RESET . " ",
				TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Click " . TextFormat::RESET . TextFormat::GRAY . "to leave your island"
			]),

			3 => VanillaBlocks::WOOL()->setColor(DyeColor::RED())->asItem()->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::RED . "NO! ")->setLore([
				TextFormat::RESET . " ",
				TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Click " . TextFormat::RESET . TextFormat::GRAY . "to cancel"
			])
		]);
		$menu->send($player);

		$menu->setListener(function(InvMenuTransaction $transaction) {
			/**
			 * @var CorePlayer $player
			 */
			$player = $transaction->getPlayer();
			if($transaction->getItemClicked()->getTypeId() == VanillaBlocks::GLAZED_TERRACOTTA()->asItem()->getTypeId()) {
				$this->getIsland($player->getCoreUser()->getIsland(), function($island) use ($player) {
					$player->islandName = null;
					$island->removeMember($player->getCoreUser());
					$island->announce(Skyblock::PREFIX . $player->getName() . " has left the island");
					$player->sendMessage(Skyblock::PREFIX . "You have successfully left your island.");
				});
			}
			return $transaction->discard();
		});
	}
}