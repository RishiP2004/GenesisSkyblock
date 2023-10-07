<?php

namespace sb\command\islands\subCmd;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use sb\islands\traits\IslandCallTrait;
use sb\lang\CustomKnownTranslationFactory;
use sb\player\CorePlayer;
use sb\Skyblock;

class DisbandSubCommand extends BaseSubCommand {
	use IslandCallTrait;

    public function __construct() {
        parent::__construct(Skyblock::getInstance(), "disband", "Disband your island.");
        $this->setPermission("pocketmine.command.me");
    }

    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    /** @var CorePlayer $sender */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $user = $sender->getCoreUser();

        if (!$user->hasIsland()) {
            $sender->sendMessage("§r§c§l(!) §r§cYou do not have an island.");
            return;
        }
		$this->getIsland($user->getIsland(), function($island) use ($sender, $args, $user) {
			if(is_null($island)) {
				$sender->sendMessage("§r§c§l(!) §r§cYou do not have an island.");
				return false;
			}
			if($island->getRole($user)->getName() != "Owner") {
				$sender->sendMessage("§r§c§l(!) You must be island leader to delete it");
				return false;
			}
			$this->sendIslandDelete($sender);
			return true;
		});
    }

	public function sendIslandDelete(Player $player) {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
		$menu->setName("Are you sure?");
		$menu->getInventory()->setContents([
			11 => VanillaBlocks::GLAZED_TERRACOTTA()->asItem()->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Yes! " . TextFormat::RESET . TextFormat::GREEN . "I am sure")->setLore([
				TextFormat::RESET . " ",
				TextFormat::RESET . TextFormat::GRAY . "This will delete your island permanently!",
				TextFormat::RESET . TextFormat::GRAY . "Once your island has been deleted there is",
				TextFormat::RESET . TextFormat::BOLD . TextFormat::RED . "NO WAY " . TextFormat::RESET . TextFormat::GRAY . "of getting it back",
				TextFormat::RESET . " ",
				TextFormat::RESET . TextFormat::WHITE . "If you do not want to delete your island",
				TextFormat::RESET . TextFormat::WHITE . "click the " . TextFormat::RED . "No " . TextFormat::WHITE . "button or close this menu!",
				TextFormat::RESET . " ",
				TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Click " . TextFormat::RESET . TextFormat::GRAY . "to disband your island"
			]),

			13 => VanillaItems::POTATO()->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::YELLOW . "Are you sure?")->setLore([
				TextFormat::RESET . TextFormat::GRAY . "There is no way of going back!"
			]),

			15 => VanillaBlocks::WOOL()->setColor(DyeColor::RED())->asItem()->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::RED . "NO! " . TextFormat::RESET . TextFormat::RED . "I am not sure")->setLore([
				TextFormat::RESET . " ",
				TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Click " . TextFormat::RESET . TextFormat::GRAY . "to cancel"
			])
		]);
		$menu->send($player);

		$menu->setListener(function(InvMenuTransaction $transaction) use ($player) {
			if($transaction->getItemClicked()->getTypeId() == VanillaBlocks::GLAZED_TERRACOTTA()->asItem()->getTypeId()) {
				$this->getIsland($player->getCoreUser()->getIsland(), function($island) use ($player) {
					$player->sendMessage(CustomKnownTranslationFactory::island_disband_success());
					$island->delete();
				});
			}
			return $transaction->discard();
		});
	}
}