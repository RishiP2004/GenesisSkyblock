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
use sb\utils\PMUtils;

class UpgradeSubCommand extends BaseSubCommand {
	use IslandCallTrait;

	public function __construct() {
		parent::__construct(Skyblock::getInstance(), "upgrade", "Upgrade your island.");
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
		$this->getIsland($user->getIsland(), function($island) use ($sender, $user, $args) {
			self::sendUpgradesMenu($sender, $island);
		});
	}

	public function sendUpgradesMenu(CorePlayer $player, Island $island) {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
		$menu->setName("Island Upgrades");
		$i = 0;
		$data=[];
		foreach($island->getUpgrades() as $upgrade) {
			$menu->getInventory()->setItem($i, VanillaItems::EMERALD()->setCustomName(TextFormat::RESET . TextFormat::LIGHT_PURPLE . $upgrade->getName())->setLore([TextFormat::RESET . TextFormat::GRAY . $upgrade->getDescription()]));
			$data[$i] = $upgrade->getName();
			$i = $i+2;
		}
		$menu->send($player);

		$menu->setListener(function(InvMenuTransaction $transaction) use ($player, $island, $data) {
			$upgrade = $island->getUpgrade($data[$transaction->getAction()->getSlot()]);
			$nextUpgradePrice = $upgrade->getNextUpgradePrice();

			return $transaction->discard()->then(function(CorePlayer $player) use($upgrade, $nextUpgradePrice) {
				$player->removeCurrentWindow();
				$player->sendForm(FormHandler::easyForm(
					$upgrade->getName() . " Upgrade",
					join("\n", [
						"Name: " . $upgrade->getName(),
						"Description: " . $upgrade->getDescription(),
						"Level: " . $upgrade->getCurrentLevel() . " / " . $upgrade->getMaxLevel(),
						"Upgrade Price: $" . ($nextUpgradePrice < 0 ? "Max Level" : number_format($nextUpgradePrice)),
						"Contributed: $" . number_format($upgrade->getAmountContributed()) . " / $" . number_format($nextUpgradePrice)
					]),
					[new EasyFormButton("Contribute", null, true, function(Player $submitter) use ($upgrade) : void {
						if ($upgrade->getNextUpgradePrice() < 1) {
							$submitter->sendMessage(TextFormat::colorize("&cThis upgrade is already at its max level."));
							return;
						}
						$user = $submitter->getCoreUser();
						$submitter->sendForm(FormHandler::textInputForm(
							"Contribute to " . $upgrade->getName(),
							"Amount to contribute",
							TextFormat::colorize("&cNot a valid amount."),
							PMUtils::genIntValidation(1, $user->getMoney()),

							function(string|int $input) use($submitter, $upgrade, $user) : void {
								$user->setMoney($user->getMoney() - $input);
								$submitter->sendMessage(TextFormat::colorize("&aYou have contributed $" . number_format($input) . " to the " . $upgrade->getName() . " upgrade for your island!"));
								$upgrade->contribute($input);
							}
						));
					})]
				));
			});
		});
	}
}