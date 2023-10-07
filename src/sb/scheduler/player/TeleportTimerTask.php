<?php

namespace sb\scheduler\player;

use pocketmine\player\OfflinePlayer;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\player\Player;
use pocketmine\entity\effect\VanillaEffects;
use sb\player\CorePlayer;
use sb\Skyblock;

class TeleportTimerTask extends Task {
	private Position $origin;

	public function __construct(private readonly Player $player, private readonly Position $position, private int $time) {
		/**@var CorePlayer $player */
		$this->player->setTeleporting(true);
		$this->origin = $player->getPosition();
		$t = ($time + 5) * 20;
		if ($t <= 0) $t = 1;

		$player->getEffects()->add(new EffectInstance(VanillaEffects::NAUSEA(), $t));
		$player->sendMessage(Skyblock::PREFIX . "You will be teleported in " . $time . "s... DON'T MOVE!");
	}

	public function onRun() : void {
		if(!$this->player->isOnline()) {
			$this->getHandler()->cancel();
			return;
		}
		if($this->origin->distance($this->player->getPosition()) >= 1){
			$this->player->setTeleporting(false);
			$this->player->sendMessage(Skyblock::ERROR_PREFIX . "Pending teleportation request cancelled due to movement");
			$this->player->getEffects()->remove(VanillaEffects::NAUSEA());
			$this->getHandler()->cancel();
			return;
		}
		if($this->time <= 0) {
			$this->player->setTeleporting(false);
			$this->player->teleport($this->position);
			$this->player->getEffects()->remove(VanillaEffects::NAUSEA());
			$this->getHandler()->cancel();
		}
		$this->time--;
	}

}