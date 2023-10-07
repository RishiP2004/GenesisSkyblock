<?php

declare(strict_types = 1);

namespace sb\command\player\rank;

use sb\player\rank\RankHandler;
use sb\Skyblock;

use sb\player\rank\Rank;

use CortexPE\Commando\BaseCommand;

use pocketmine\command\CommandSender;

use pocketmine\utils\TextFormat;

class RanksCommand extends BaseCommand {
	public function prepare() : void {
		$this->setPermission("pocketmine.command.me");
	}
    
    public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
        $sender->sendMessage(Skyblock::PREFIX . "Ranks:");
            
        foreach(RankHandler::getAll() as $rank) {
        	if($rank instanceof Rank) {
        		$sender->sendMessage(TextFormat::GRAY . "- " . $rank->getColor() . $rank->getName());
        	}
        }
    }
}
