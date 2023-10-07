<?php

namespace sb\player\rank;

use sb\command\player\rank\RankCommand;
use sb\command\player\rank\RanksCommand;
use sb\command\player\rank\SetRankCommand;
use sb\player\PlayerManager;
use sb\Skyblock;

class RankHandler implements Ranks {
	private static array $ranks = [];

	public function __construct() {
		foreach(self::RANKS as $name => $data) {
			if(is_null($data["inheritance"])) $inheritance = null;
			else $inheritance = self::get($data["inheritance"]);

			$rank = new Rank(
				$name,
				$data["color"],
				$data["permissions"],
				$inheritance
			);
			self::init($rank);
		}
	}

	private static function init(Rank $rank) : void {
		self::$ranks[strtolower($rank->getName())] = $rank;
	}

	public static function getAll() : array {
		return self::$ranks;
	}

	public static function get(string $rank) : Rank | null {
		return self::$ranks[strtolower($rank)] ?? null;
	}
}