<?php

namespace sb\command\islands\subCmd;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\VanillaBlocks;
use sb\player\CorePlayer;
use sb\utils\PMUtils;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use sb\form\FormHandler;
use sb\islands\utils\IslandGenerators;
use sb\islands\IslandManager;
use sb\islands\traits\IslandCallTrait;
use sb\Skyblock;
use CortexPE\Commando\constraint\InGameRequiredConstraint;

class CreateSubCommand extends BaseSubCommand {
	use IslandCallTrait;

    public function __construct() {
        parent::__construct(Skyblock::getInstance(), "create", "Create an island.");
        $this->setPermission("pocketmine.command.me");
    }

    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
    }


    /** @var CorePlayer $sender */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $user = $sender->getCoreUser();

        if ($user->hasIsland()) {
            $sender->sendMessage(TextFormat::colorize("&r&c&l(!) &r&cYou already have an island."));
			$sender->sendMessage(TextFormat::colorize("&r&7To delete an island, use &r&7/is disband, this action is irreversible."));
            return;
        }
		$sender->sendForm(FormHandler::textInputForm(
			"Island Creation",
			"Island Name",
			TextFormat::colorize("&r&c&l(!) &r&cIsland names must have all real characters, and be 2-12 characters in length."),
			PMUtils::genValidation(2, 12),
			function($name) use($sender) : void {
				$this->getIsland($name, function($island) use ($sender, $name) {
					if(!is_null($island)) {
						$sender->sendMessage(TextFormat::colorize("&r&c&l(!) &r&c '{$name}' is already an island."));
						return false;
					}
					$this->sendIslandCreateMenu($sender, $name);
					return true;
				});
			}
		));
    }

	public function sendIslandCreateMenu(CorePlayer $player, string $name) {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
		$menu->setName("Pick your island");
		$menu->getInventory()->setContents([
			10 => VanillaBlocks::GRASS()->asItem()->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "The Original " . TextFormat::WHITE . "Island")->setLore([
				TextFormat::RESET . TextFormat::GRAY . "Play on the original SkyBlock island.",
				TextFormat::RESET . "",
				TextFormat::RESET . TextFormat::GRAY . "Are you sure you want this as your island?",
				TextFormat::RESET . TextFormat::GRAY . "You can not change your choice!",
				TextFormat::RESET . "",
				TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Click " . TextFormat::RESET . TextFormat::GRAY . "to choose"
			]),

			11 => VanillaBlocks::SNOW()->asItem()->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::AQUA . "Snow " . TextFormat::WHITE . "Island")->setLore([
				TextFormat::RESET . TextFormat::GRAY . "If you like snow you should play",
				TextFormat::RESET . TextFormat::GRAY . "on this island",
				TextFormat::RESET . "",
				TextFormat::RESET . TextFormat::GRAY . "Are you sure you want this as your island?",
				TextFormat::RESET . TextFormat::GRAY . "You can not change your choice!",
				TextFormat::RESET . "",
				TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Click " . TextFormat::RESET . TextFormat::GRAY . "to choose"
			]),

			12 => VanillaBlocks::ACACIA_LEAVES()->asItem()->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::YELLOW . "Savanna " . TextFormat::WHITE . "Island")->setLore([
                TextFormat::RESET . TextFormat::GRAY . "A savanna based island.",
                TextFormat::RESET . "",
                TextFormat::RESET . TextFormat::GRAY . "Are you sure you want this as your island?",
                TextFormat::RESET . TextFormat::GRAY . "You can not change your choice!",
                TextFormat::RESET . "",
                TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Click " . TextFormat::RESET . TextFormat::GRAY . "to choose"
			]),

			13 => VanillaBlocks::SAND()->asItem()->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::GOLD . "Desert " . TextFormat::WHITE . "Island")->setLore([
                TextFormat::RESET . TextFormat::GRAY . "Can you take the heat?",
                TextFormat::RESET . "",
                TextFormat::RESET . TextFormat::GRAY . "Are you sure you want this as your island?",
                TextFormat::RESET . TextFormat::GRAY . "You can not change your choice!",
                TextFormat::RESET . "",
                TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Click " . TextFormat::RESET . TextFormat::GRAY . "to choose"
			]),

			14 => VanillaBlocks::BIRCH_SAPLING()->asItem()->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::DARK_GREEN . "Forest " . TextFormat::WHITE . "Island")->setLore([
                TextFormat::RESET . TextFormat::GRAY . "Hope you like the shade!",
                TextFormat::RESET . "",
                TextFormat::RESET . TextFormat::GRAY . "Are you sure you want this as your island?",
                TextFormat::RESET . TextFormat::GRAY . "You can not change your choice!",
                TextFormat::RESET . "",
                TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Click " . TextFormat::RESET . TextFormat::GRAY . "to choose"
			]),

			15 => VanillaBlocks::AZURE_BLUET()->asItem()->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::RED . "Oriental " . TextFormat::WHITE . "Island")->setLore([
                TextFormat::RESET . TextFormat::GRAY . "Play on a East Asian Island",
                TextFormat::RESET . "",
                TextFormat::RESET . TextFormat::GRAY . "Are you sure you want this as your island?",
                TextFormat::RESET . TextFormat::GRAY . "You can not change your choice!",
                TextFormat::RESET . "",
                TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Click " . TextFormat::RESET . TextFormat::GRAY . "to choose"
			]),

			16 => VanillaBlocks::GLAZED_TERRACOTTA()->asItem()->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::DARK_RED . "BadLands " . TextFormat::WHITE . "Island")->setLore([
                TextFormat::RESET . TextFormat::GRAY . "Don't touch that mouse...",
                TextFormat::RESET . "",
                TextFormat::RESET . TextFormat::GRAY . "Are you sure you want this as your island?",
                TextFormat::RESET . TextFormat::GRAY . "You can not change your choice!",
                TextFormat::RESET . "",
                TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Click " . TextFormat::RESET . TextFormat::GRAY . "to choose"
			]),
		]);
		$menu->send($player);

		$menu->setListener(function(InvMenuTransaction $transaction) use ($name, $player) {
			switch($transaction->getItemClicked()->getTypeId()) {
				case VanillaBlocks::GRASS()->asItem()->getTypeId():
					$type = IslandGenerators::GEN_BASIC;
				break;
				case VanillaBlocks::SNOW()->asItem()->getTypeId():
					$type = IslandGenerators::GEN_SNOW;
				break;
				case VanillaBlocks::ACACIA_LEAVES()->asItem()->getTypeId():
					$type = IslandGenerators::GEN_SAVANNA;
					break;
				case VanillaBlocks::SAND()->asItem()->getTypeId():
					$type = IslandGenerators::GEN_DESERT;
					break;
				case VanillaBlocks::BIRCH_SAPLING()->asItem()->getTypeId():
					$type = IslandGenerators::GEN_FOREST;
					break;
				case VanillaBlocks::AZURE_BLUET()->asItem()->getTypeId():
					$type = IslandGenerators::GEN_ORIENTAL;
					break;
				case VanillaBlocks::GLAZED_TERRACOTTA()->asItem()->getTypeId():
					$type = IslandGenerators::GEN_BADLANDS;
					break;
			}
			IslandManager::getInstance()->createIsland($player, $name, $type);
			return $transaction->discard()->then(
				function(CorePlayer $player) : void {
					$player->removeCurrentWindow();
				});
		});
	}
}