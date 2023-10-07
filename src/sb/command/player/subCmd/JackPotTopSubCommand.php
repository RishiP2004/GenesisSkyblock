<?php

namespace sb\command\player\subCmd;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use sb\player\CorePlayer;
use sb\server\jackpot\JackpotHandler;
use sb\server\jackpot\JackpotSortableData;
use sb\Skyblock;

class JackPotTopSubCommand extends BaseSubCommand {

	protected function prepare(): void {
		$this->setPermission("pocketmine.command.me");
		$this->registerArgument(0, new IntegerArgument("page",true));
	}

	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		$page = $args["page"] ?? 1;
		JackpotSortableData::getTopWins(5, $page, function($top) use($sender, $args, $page) {
			JackpotSortableData::getTopEarnings(5, $page, function($earnings) use($sender, $args, $page, $top) {
				if(empty($top) or empty($earnings)) {
					$sender->sendMessage(Skyblock::ERROR_PREFIX . "No accounts registered");
					return;
				}
				$message = "§r§d§lTop Jackpot Winners (§b" . $page . "§d§l)";

				for($i = 0; $i < count($top); ++$i) {
					$message .= "\n" . "§r§d§l" . $i + 1 . ". §r§f" . array_keys($top)[$i] . " §r§d- §r§b§l " . array_values($top)[$i] . " §r§bWins §l(§r§d$" . array_values($earnings)[$i] . "§b§l)";
				}
				$sender->sendMessage($message);
			});
		});
	}
}