<?php

declare(strict_types=1);

namespace sb\player\rank;

use pocketmine\player\chat\ChatFormatter;
use sb\islands\traits\IslandCallTrait;
use sb\player\CorePlayer;

final class RankChatter implements ChatFormatter {
	use IslandCallTrait;

	public function __construct(
		private CorePlayer $player
	) {
	}

	public function format(string $username, string $message) : string {
		return $this->player->getCoreUser()->getRank()->getChatFormatFor($this->player, $message);
	}
}