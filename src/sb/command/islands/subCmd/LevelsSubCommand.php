<?php

namespace sb\command\islands\subCmd;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sb\form\EasyFormButton;
use sb\form\FormHandler;
use sb\islands\Island;
use sb\islands\traits\IslandCallTrait;
use sb\islands\utils\IslandStats;
use sb\player\CorePlayer;
use sb\Skyblock;
use sb\utils\PMUtils;

class LevelsSubCommand extends BaseSubCommand {
	use IslandCallTrait;

	public function __construct() {
		parent::__construct(Skyblock::getInstance(), "levels", "Island levels");
		$this->setPermission("pocketmine.command.me");
	}

	public function prepare() : void {
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	/** @var CorePlayer $sender */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		$user = $sender->getCoreUser();

		if (!$user->hasIsland()) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have an island.");
			return;
		}
		$this->getIsland($user->getIsland(), function($island) use ($sender, $user, $args) {
			self::sendLevelsForm($sender, $island);
		});
	}

	public function sendLevelsForm(CorePlayer $player, Island $island) {
		$player->sendForm(FormHandler::easyForm(
			"Island Levels",
			join("\n", [
				"§r§7Island Level: §a" . $island->getLevel(),
				"§r§7Island Xp: §a" . $island->getXp() . "§7/§4" . (string) $this->getXpNeeded($island->getLevel()+1),
				"§r§7Progress: §7[" . $this->translateProgress($island) . "§7] §7(" . $this->getPctProgression($island) . "%§7)",
				"\n",
				"§r§7Island stats:",
				"§r§7Fish count: §a" . $island->getStat(IslandStats::FISH_COUNT),
				"§r§7Blocks mined: §a" . $island->getStat(IslandStats::BLOCKS_MINED),
				"§r§7Mobs killed (AFK): §a" . $island->getStat(IslandStats::MOBS_KILLED_AFK),
				"§r§7Mobs killed (manually): §a" . $island->getStat(IslandStats::MOBS_KILLED_AFK),
			]),
			[new EasyFormButton("Level Up", null, true, function(Player $submitter) use ($island) : void {
				if($island->getLevel() == 50) {
					$submitter->sendMessage(TextFormat::colorize("&cThis upgrade is already at its max level."));
					return;
				}
				if($island->getXp() < $this->getXpNeeded($island->getLevel()+1)) {
					$submitter->sendMessage(TextFormat::colorize("&cYou do not have enough Xp to upgrade"));
					return;
				}
				$island->setXp(0);
				$island->levelUp();
			})]
		));
	}

	public function getXpNeeded(int $level) :int {
		return $level * 500;
	}

	public function translateProgress(Island $island) : string {

		$red = str_repeat("|", ($this->getXpNeeded($island->getLevel()+1) - $island->getXp()) / 500);
		$green = str_repeat("|", $island->getXp() / 500);
		return TextFormat::GREEN . $green . TextFormat::RED . $red;
	}

	public function getPctProgression(Island $island) : string {
		if($island->getXp() == 0) return "§a0";
		return (string) "§a" . ($island->getXp() / $this->getXpNeeded($island->getLevel() + 1) * 100);
	}
}