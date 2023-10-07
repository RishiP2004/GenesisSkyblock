<?php

namespace sb\scheduler\player;

use pocketmine\scheduler\Task;

use pocketmine\utils\TextFormat;
use sb\player\CorePlayer;
use sb\Skyblock;

class CombatTagTask extends Task {
	public function __construct(protected CorePlayer $damager, protected CorePlayer $player) {
		$player->combatTag();
		$player->setAllowFlight(false);
		$player->setFlying(false);
		$player->sendMessage(Skyblock::ERROR_PREFIX . "You have entered combat. Do not log out for 15s.");
		$this->player->sendTip('§l§bCombat§r§7: §l§8[§r' . $this->calculateCombatTag() . '§l§8]');
	}

	private function calculateCombatTag() : string {
		$red = str_repeat("|", 15 - $this->player->getCombatTagTime()); //Change 15 with the maximum combat tag timer
		$green = str_repeat("|", $this->player->getCombatTagTime());
		return TextFormat::GREEN . $green . TextFormat::RED . $red;
	}

	public function onRun() : void {
		$player = $this->player;

		if(!$player->isOnline()) {
			$this->getHandler()->cancel();
			return;
		}
		if(!$player->isCombatTagged() || $player->getCombatTagTime() === 0 || !$this->player->isAlive()) {
			$this->player->setAllowFlight(true);
			$player->combatTag(false);
			$this->player->sendMessage("§r§a§l(!) §r§aYou have left combat. You may now safely logout.");
			$this->getHandler()->cancel();
		} else {
			$player->setCombatTagTime($player->getCombatTagTime() - 1);
			$player->sendTip('§l§bCombat§r§7: §l§8[§r' . $this->calculateCombatTag() . '§l§8]');
		}
	}
}
