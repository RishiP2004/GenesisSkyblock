<?php

declare(strict_types=1);

namespace sb\command\world\islandCache;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Label;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sb\command\world\islandCache\subCmd\SpawnSubCommand;
use sb\player\CorePlayer;
use sb\utils\MathUtils;
use sb\world\islandCache\IslandCacheHandler;

class IslandCacheCommand extends BaseCommand {
	public function prepare(): void {
		$this->setPermission("pocketmine.command.me");
		$this->addConstraint(new InGameRequiredConstraint($this));
		$this->registerSubCommand(new  SpawnSubCommand());
	}

	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		$count = 0;
		$lines = [];

		foreach(IslandCacheHandler::getAll() as $islandCacheData) {
			$position = $islandCacheData->getBlock()->getPosition();
			$x = $position->getFloorX();
			$y = $position->getFloorY();
			$z = $position->getFloorZ();
			$time = MathUtils::getFormattedTime($islandCacheData->getTimer());
			$lines[] = new Label((string)$count, TextFormat::AQUA . "Island Cache ($x, $y, $z): " . TextFormat::WHITE . $time .  " before despawn.");
			$count++;
		}
		$sender->sendForm(
			new CustomForm(
				TextFormat::AQUA . "Island Caches",
				$lines,
				function(Player $player, CustomFormResponse $response) : void {}
			)
		);
	}
}
