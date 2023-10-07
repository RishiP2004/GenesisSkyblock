<?php

declare(strict_types = 1);

namespace sb\scheduler\server\broadcast;

use sb\Skyblock;

use sb\player\CorePlayer;

use sb\server\broadcast\BroadcastHandler;

use pocketmine\scheduler\Task;

class BroadcastSendTask extends Task {
	private int $current = 0;

    public function __construct(
		private readonly string      $type,
		private readonly ?CorePlayer $player,
		private readonly int         $duration = 0,
		private readonly string      $display = "",
		private readonly string      $display2 = ""
	) {}

    public function onRun() : void {
        if($this->current <= $this->duration) {
			$this->getHandler()->cancel();
        }
        switch($this->type) {
            case BroadcastHandler::POPUP:
                if($this->player instanceof CorePlayer) {
                    $this->player->sendPopup(str_replace("{PLAYER}", $this->player->getName(), $this->display));
                } else {
                    foreach(Skyblock::getInstance()->getServer()->getOnlinePlayers() as $players) {
                        $players->sendPopup(str_replace("{PLAYER}", "*", $this->display));
                    }
                }
            break;
            case BroadcastHandler::TITLE:
                if($this->player instanceof CorePlayer) {
                    $this->player->sendTitle(str_replace("{PLAYER}", $this->player->getName(), $this->display));
                    $this->player->sendSubTitle(str_replace("{PLAYER}", $this->player->getName(), $this->display2));
                } else {
                    foreach(Skyblock::getInstance()->getServer()->getOnlinePlayers() as $player) {
                        $player->sendTitle(str_replace("{PLAYER}", "*", $this->display));
                        $player->sendSubTitle(str_replace("{PLAYER}", "*", $this->display2));
                    }
                }
            break;
        }
        $this->current += 1;
    }
}

