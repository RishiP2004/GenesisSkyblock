<?php

declare(strict_types = 1);

namespace sb\command\player\fund;

use dktapps\pmforms\MenuForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as C;
use sb\player\CorePlayer;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use sb\server\fund\Fund;
use sb\server\fund\FundHandler;

class FundCommand extends BaseCommand {
	public function prepare() : void {
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->setPermission("pocketmine.command.me");
	}
	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		if(!$sender instanceof CorePlayer) return;
		/**WARNING: I did this in like 15 minutes its ass code, but it won't affect anyone*/

		$funds = FundHandler::getInstance()->getFunds();

		$form = new MenuForm(FundHandler::TITLE, implode("\n", [C::RESET . C::GRAY . "Add money to the /fund and unlock new", C::RESET . C::GRAY . "content for your entire realm with", C::RESET . C::GRAY . "/fund deposit <$>"]), [], function (Player $player, int $selectedOption) : void {});
		$form->setHandler(function (Player $player, int $data) use ($funds): void{
			$f = array_values($funds)[$data];
			$form = self::displayFund($f);

			$player->sendForm($form);
		});

		foreach ($funds as $fund) {
			$buttonText = $fund->getFormButton(); // Fixed the method call
			$form->addButton(TextFormat::colorize($buttonText . "\n". "&r&8Click to view fund"));
		}

		$sender->sendForm($form);
	}

	protected static function displayFund(Fund $fund): MenuForm {
		$locked = $fund->getProgress() >= $fund->getGoal() ? C::GREEN . "UNLOCKED" : C::RED . C::BOLD . "LOCKED";
		$description = str_replace(["{progress}", "{max}", "{percentage}", "{status}"], [$fund->getProgress(), $fund->getGoal(), $fund->getPercentage(), $locked], $fund->getDescription());

		$form = new MenuForm(FundHandler::TITLE, TextFormat::colorize(implode("\n", $description)), [], function (Player $player, int $selectedOption) use ($fund): void {
			if($selectedOption === 2){
				$player->chat("/fund");
			}else{
				/** @var CorePlayer $player */
				$coreUser = $player->getCoreUser();

				if($coreUser->getMoney() < 10000){
					$player->sendMessage(TextFormat::colorize("&r&c&l(!) &r&cYou do not have enough money to donate to this fund."));
					return;
				}
				if($fund->isCompleted()){
					$player->sendMessage(TextFormat::colorize("&r&c&l(!) &r&cThis fund has already been completed."));
					return;
				}
				$player->sendMessage(TextFormat::colorize("&r&c&l- $10,000"));
				$coreUser->reduceMoney(10000);
				$fund->addProgress(10000);
			}
		});
		$form->addButton(C::RESET . C::BOLD . C::DARK_GRAY . "DONATE $10K" . "\n" . C::RESET . C::GRAY . "Click to donate.", null);
		$form->addButton(C::RESET . C::BOLD . C::DARK_GRAY . "BACK", null);

		return $form;
	}
}