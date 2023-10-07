<?php

declare(strict_types = 1);

namespace sb\scheduler;

use pocketmine\scheduler\Task;
use sb\player\PlayerManager;
use sb\server\ServerManager;
use sb\world\WorldManager;
//todo: allow managers to append?
class CoreScheduler extends Task {
    public function onRun() : void {
		//this is so ass, the
		//check for
		WorldManager::getInstance()->tick();
		ServerManager::getInstance()->tick();
		PlayerManager::getInstance()->tick();
    }
}