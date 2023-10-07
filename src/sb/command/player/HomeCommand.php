<?php

declare(strict_types = 1);

namespace sb\command\player;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use pocketmine\Server;
use sb\scheduler\player\TeleportTimerTask;
use sb\Skyblock;

use sb\player\traits\PlayerCallTrait;

use pocketmine\command\CommandSender;
//todo
class HomeCommand extends BaseCommand {
	use PlayerCallTrait;

	public function prepare() : void {
		$this->setPermission("pocketmine.command.me");
		$this->registerArgument(0, new RawStringArgument("name", false));
	}

	public function onRun(CommandSender $sender, string $commandLabel, array $args) : void {
		$this->getCoreUser($sender->getName(), function($user) use ($sender, $args) {
			if($user->getHome($args["name"]) === null){
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "The entered home name does not exist.");
				return false;
			} else {
				$timer = 7;

				$pos = $user->getHome($args["home"]);

				$world = Server::getInstance()->getWorldManager()->getDefaultWorld();
				$sender->sendMessage(Skyblock::PREFIX . "Teleporting To home: " . $args["home"]);
				$world->orderChunkPopulation($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4, null)->onCompletion(function() use($sender, $world, $pos, $timer) : void {
					if($sender !== null){
						Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new TeleportTimerTask($sender, $pos, $timer), 20);
					}
				}, function() : void{
				});
			}
			return true;
		});
	}
}
