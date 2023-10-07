<?php

namespace sb\command\player;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use sb\scheduler\player\TeleportTimerTask;
use sb\Skyblock;

class SpawnCommand extends BaseCommand {
	public function prepare() : void {
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->setPermission("pocketmine.command.me");
	}

	public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$timer = 7;
		$pos = Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation();

		$world = Server::getInstance()->getWorldManager()->getDefaultWorld();

		$world->orderChunkPopulation($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4, null)->onCompletion(function() use($sender, $world, $pos, $timer) : void {
			$sender->sendMessage(Skyblock::PREFIX . "Teleporting To Spawn");
			if($sender !== null){
				Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new TeleportTimerTask($sender, $pos, $timer), 20);
			}
		}, function() : void{
		});
	}
}