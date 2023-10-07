<?php

declare(strict_types=1);

namespace sb\command\player;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use sb\command\player\subCmd\JackPotBuySubCommand;
use sb\command\player\subCmd\JackPotStatsSubCommand;
use sb\command\player\subCmd\JackPotTopSubCommand;
use sb\server\jackpot\JackpotHandler;
use sb\Skyblock;
use sb\utils\MathUtils;

class JackPotCommand extends BaseCommand {
	public function prepare() : void {
		$this->setPermission("pocketmine.command.me");
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->registerSubCommand(new JackPotTopSubCommand(Skyblock::getInstance(), "top"));
		$this->registerSubCommand(new JackPotStatsSubCommand(Skyblock::getInstance(), "stats"));
		$this->registerSubCommand(new JackPotBuySubCommand(Skyblock::getInstance(), "buy"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		$percentage = 0;
		$value = number_format(JackpotHandler::getPrizePool(), 2);
		$mytickets = JackpotHandler::getTickets($sender->getName());
		$tickets = [];
		foreach (JackpotHandler::$players as $playera => $amount) {
			for ($i = 0; $i < $amount; $i++) {
				$tickets[] = $sender;
			}
		}
		$ticketz = number_format(count($tickets), 2);
		if ($mytickets >= 1) $percentage = ($mytickets / count($tickets)) * 100;

		$sender->sendMessage("§r§d§lGenesis Jackpot");
		$sender->sendMessage("§r§b§lJackpot Value§r§b: §r§d$$value §r§7(-10% tax)");
		$sender->sendMessage("§r§b§lTickets Sold§r§b: §r§e$ticketz");
		$sender->sendMessage("§r§b§lYour Tickets§b: §r§a$mytickets §r§7($percentage%)");
		$sender->sendMessage("\n");
		$sender->sendMessage("§r§b§l(!) §r§bNext winner in " . MathUtils::secondsToTime(JackpotHandler::$time)['d'] . " days, " . MathUtils::secondsToTime(JackpotHandler::$time)['h'] . " hours," . MathUtils::secondsToTime(JackpotHandler::$time)['s'] . " seconds");
	}
}