<?php

declare(strict_types=1);

namespace sb\command\islands\subCmd;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Label;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use sb\islands\Island;
use sb\islands\IslandManager;
use sb\islands\traits\IslandCallTrait;
use sb\player\CorePlayer;
use sb\player\traits\PlayerCallTrait;
use sb\Skyblock;
use CortexPE\Commando\constraint\InGameRequiredConstraint;

class InfoSubCommand extends BaseSubCommand {
	use PlayerCallTrait;
	use IslandCallTrait;


	public function __construct() {
		parent::__construct(Skyblock::getInstance(), "info", "See an Island's info");
		$this->setPermission("pocketmine.command.me");
		$this->registerArgument(0, new RawStringArgument("island/player", true));
	}

	public function prepare() : void {
		$this->addConstraint(new InGameRequiredConstraint($this));
	}
	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$island = $sender->getIslandName();
		if(!isset($args["island/player"])){

			if($island === "") {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "Please provide an island or player name");
				return;
			}
			self::sendInfoForm($sender, IslandManager::getInstance()->getIslandFromName($island));
			return;
		}
		$looking = $args["island/player"];

		$this->getCoreUser($looking, function($user) use ($island, $sender, $looking) {
			if (is_null($user)) {
				$this->getIsland($looking, function($island) use ($sender, $looking) {
					if (is_null($island)) {
						$sender->sendMessage(Skyblock::ERROR_PREFIX . "No island or player named §c" . $looking . "§7was found");
					} else {
						self::sendInfoForm($sender, $island);
					}
				});
			} else {
				self::sendInfoForm($sender, IslandManager::getInstance()->getIslandFromName($island));
			}
		});
	}

	public static function sendInfoForm(CorePlayer $player, Island $island) {
		$info = [];
		$leader = $island->getLeader();
		$members = [];

		foreach($island->getMembers() as $member) {
			$members[] = $member->getName();
		}

		$info[] = "§7Island Name:§c " . $island->getName();
		$info[] = "§7Owner: §c" . Server::getInstance()->getPlayerExact($leader) instanceof CorePlayer ? "§a$leader" : "§c$leader";
		$str = count($members) . "§7/" . $island->getMaxMembers();

		if(count($members) === 0){
			$info[] = "§cNone";
		} else $info[] = "§7Members (§c{$str}§7): §c" . implode(", ", array_map(fn(string $member): string => ( Server::getInstance()->getPlayerExact($leader) instanceof CorePlayer ? "§a$member, " : "§c$member, "), $members));

		$info[] = "§7Island Value: §c" . number_format($island->getValue());
		$info[] = "§7Island Power: §c" . number_format($island->getPower());
		$info[] = "§r";
		$size = (20 * ($island->getUpgrade("Size")->getCurrentLevel() + 1));
		$info[] = "§7Island Size: §c{$size}x{$size}";
		$info[] = "§7Hopper Limit: §c10" . "§7/§c" . ($island->getUpgrade("Hopper Limit")->getCurrentLevel() * 20);
		$info[] = "§7Spawner Limit: §c10" .  "§7/§c" . ($island->getUpgrade("Spawner Limit")->getCurrentLevel() * 20);
		$labels = [];
		$count = 0;
		foreach($info as $in) {
			$labels[] = new Label((string)$count, $in);
			$count++;
		}

		$player->sendForm(
			new CustomForm(
				TextFormat::GREEN . $island->getName() . "'s info",
				$labels,
				function(Player $player, CustomFormResponse $response) : void {}
			)
		);
	}
}