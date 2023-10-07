<?php

declare(strict_types = 1);

namespace sb\server\broadcast;

use pocketmine\utils\TextFormat;

interface Broadcasts {
	const FORMATS = [
		"date_time" => "H:i:s",
		"broadcast" => "{PREFIX}" . TextFormat::GRAY . "{MESSAGE}"
	];
	const AUTOS = [
		"message" => true,
		"popup" => false,
		"title" => false,
	];
	const TIMES = [
		"message" => 1,
		"popup" => 45,
		"title" => 45,
	];
	const DURATIONS = [
		"popup" => 5,
		"title" => 5
	];
	const MESSAGES = [
		 "         \n                 §r§b§lSaturn §r§8| §r§b§lAnnouncer \n    §r§fFollow us on Twitter: §5@GenesisNetwork",
		 "         \n                 §r§b§lSaturn §r§8| §r§b§lAnnouncer \n    §r§fThe server is currently in BETA, please report any bugs to our discord.",
		 "         \n                 §r§b§lSaturn §r§8| §r§b§lAnnouncer \n    §r§fJoin our discord server, so you can stay up to date with the latest news",
	];
	const POPUPS = [
		"",
		"",
		""
	];
	const TITLES = [
		"",
		"",
		""
	];
	const JOINS = [
		"first" => TextFormat::BLUE . "Welcome {PLAYER} to the community.",
		"normal" => TextFormat::AQUA . "+ {PLAYER}",
	];
	const DEATHS = [
		"contact" => "{PLAYER}" . TextFormat::GRAY . " was killed by {BLOCK}",
		"kill" => "{PLAYER}" . TextFormat::GRAY . " was killed by {KILLER}",
		"projectile" => "{PLAYER}" . TextFormat::GRAY . " was killed by {KILLER}",
		"suffocation" => "{PLAYER}" . TextFormat::GRAY . " suffocated",
		"starvation" => "{PLAYER}" . TextFormat::GRAY . " starved to death",
		"fall" => "{PLAYER}" . TextFormat::GRAY . " fell from a high distance",
		"fire" => "{PLAYER}" . TextFormat::GRAY . " went up in flames",
		"on-fire" => "{PLAYER}" . TextFormat::GRAY . " burned",
		"lava" => "{PLAYER}" . TextFormat::GRAY . " tried to swim in lava",
		"drowning" => "{PLAYER}" . TextFormat::GRAY . " rowned",
		"explosion" => "{PLAYER}" . TextFormat::GRAY . " exploded",
		"void" => "{PLAYER}" . TextFormat::GRAY . " fell into the void",
		"suicide" => "{PLAYER}" . TextFormat::GRAY . " committed suicide",
		"magic" => "{PLAYER}" . TextFormat::GRAY . " was killed by magic",
		"normal" => "{PLAYER}" . TextFormat::GRAY . " died"
	];
	const QUITS = [
		"normal" => TextFormat::RED . "- {PLAYER}"
	];
	const KICKS = [
		"outdated" => [
			"client" => "{PREFIX}Your Minecraft client is outdated",
			"server" => "{PREFIX}This server is outdated"
		],
		"whitelisted" => "{PREFIX}This server is whitelisted",
		"full" => "{PREFIX}This server is full {ONLINE_PLAYERS}/{MAX_PLAYERS}"
	];
}