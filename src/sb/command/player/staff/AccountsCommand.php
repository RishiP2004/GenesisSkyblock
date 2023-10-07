<?php

declare(strict_types = 1);

namespace sb\command\player\staff;

use sb\Skyblock;

use sb\player\PlayerManager;

use CortexPE\Commando\BaseCommand;

use pocketmine\command\CommandSender;

use pocketmine\utils\TextFormat;

class AccountsCommand extends BaseCommand {
    public function prepare() : void {
		$this->setPermission("accounts.command");
		$this->setAliases(["accs"]);
	}

    public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		PlayerManager::getInstance()->getAllData(function($users) use($sender) {
			$sender->sendMessage(Skyblock::PREFIX . "Total Accounts Registered (x" . count($users) . ")");
				
			$allUsers = [];
				
			foreach($users as $user) {
				$allUsers[] = $user->getName();
			}
			$sender->sendMessage(TextFormat::GRAY . implode(", ", $allUsers));
		});
    }
}