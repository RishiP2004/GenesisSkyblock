<?php

declare(strict_types = 1);

namespace sb\command\player;

use sb\player\PlayerSortableData;
use sb\Skyblock;

use sb\player\PlayerManager;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\IntegerArgument;

use pocketmine\command\CommandSender;

use pocketmine\utils\TextFormat;

class TopMoneyCommand extends BaseCommand {
	public function prepare() : void {
		$this->setPermission("pocketmine.command.me");
		$this->registerArgument(0, new IntegerArgument("page", true));
	}

    public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$page = $args["page"] ?? 1;
		PlayerSortableData::getTopMoney(5, $page, function($top) use($sender, $args, $page) {
			if(empty($top)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "No accounts registered");
				return;
			}
			$message = Skyblock::PREFIX . "Top Money (" . $page . ")";

			for($i = 0; $i < count($top); ++$i) {
				$message .= TextFormat::EOL . TextFormat::GOLD . $i + 1 . ". " . TextFormat::GRAY . array_keys($top)[$i] . ": " . TextFormat::GREEN  . "$" . array_values($top)[$i];
			}
			$sender->sendMessage($message);
		});
	}
}