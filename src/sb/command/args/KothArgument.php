<?php

namespace sb\command\args;

use CortexPE\Commando\args\StringEnumArgument;

use pocketmine\command\CommandSender;
use sb\world\koth\KothHandler;
use sb\world\koth\Koth;

class KothArgument extends StringEnumArgument {
	public function parse(string $argument, CommandSender $sender) : mixed {
		return KothHandler::get(strtolower($argument));
	}

	public function getEnumName() : string {
		return "koth";
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		return array_values(array_map(fn(Koth $koth) => strtolower($koth->getName()), KothHandler::getAll()));
	}
}