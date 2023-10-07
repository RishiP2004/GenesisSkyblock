<?php

namespace sb\command\args;

use CortexPE\Commando\args\StringEnumArgument;

use pocketmine\command\CommandSender;
use sb\player\rank\Rank;
use sb\player\rank\RankHandler;

class RankArgument extends StringEnumArgument {
	public function parse(string $argument, CommandSender $sender) : mixed {
		return RankHandler::get(strtolower($argument));
	}

	public function getEnumName() : string {
		return "rank";
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		return array_values(array_map(fn(Rank $rank) => strtolower($rank->getName()), RankHandler::getAll()));
	}
}