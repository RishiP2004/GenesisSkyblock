<?php

namespace sb\player\rank;

interface Ranks {
	const RANKS = [
		"Member" => [
			"color" => "§l§7",
			"permissions" => [
				"pv.member", //2
				"echest.use", //todo
				"kit.starter",
				"genesis.kit.member",
			],
			"inheritance" => null,
		],
		"Mercury" => [
			"color" => "§l§3",
			"permissions" => [
				"pv.mercury", //4
				"kit.mercury",
				"genesis.kit.mercury",
				"genesis.kit.member",
			],
			"inheritance" => null,
		],
		"EarthKit" => [
			"color" => "§l§2",
			"permissions" => [
				"pv.mercury", //4
				"genesis.kit.earth",
				"genesis.kit.mercury",
				"genesis.kit.member",
			],
			"inheritance" => null,
		],
		"Mars" => [
			"color" => "§l§6",
			"permissions" => [
				"pv.mars", //6
				"genesis.kit.mars",
				"genesis.kit.mercury",
				"genesis.kit.member",
				"genesis.kit.earth",
			],
			"inheritance" => null,
		],
		"Jupiter" => [
			"color" => "§l§u",
			"permissions" => [
				"pv.mars", //6
				"kit.jupiter",
				"genesis.kit.jupiter",
				"genesis.kit.mars",
				"genesis.kit.mercury",
				"genesis.kit.member",
				"genesis.kit.earth",
			],
			"inheritance" => null,
		],
		"Saturn" => [
			"color" => "§l§b",
			"permissions" => [
				"pv.mercury", //6
				"kit.saturn",
				"genesis.kit.saturn",
				"genesis.kit.jupiter",
				"genesis.kit.mars",
				"genesis.kit.mercury",
				"genesis.kit.member",
				"genesis.kit.earth",
			],
			"inheritance" => null,
		],
		"Saturn+" => [
			"color" => "§l§b",
			"permissions" => [
				"pv.mercury", //8
				"kit.saturn+",
				"genesis.kit.saturn",
				"genesis.kit.jupiter",
				"genesis.kit.mars",
				"genesis.kit.mercury",
				"genesis.kit.member",
				"genesis.kit.earth",
			],
			"inheritance" => null,
		],
		"Trainee" => [
			"color" => "§l§b",
			"permissions" => [
				"pocketmine.command.me",
				"genesis.kit.saturn",
				"genesis.kit.jupiter",
				"genesis.kit.mars",
				"genesis.kit.mercury",
				"genesis.kit.member",
				"genesis.kit.earth",
			],
			"inheritance" => null,
		],
		"JrMod" => [
			"color" => "§l§u",
			"permissions" => [
				"pocketmine.command.me",
				"genesis.kit.saturn",
				"genesis.kit.jupiter",
				"genesis.kit.mars",
				"genesis.kit.mercury",
				"genesis.kit.member",
				"genesis.kit.earth",
			],
			"inheritance" => null,
		],
		"Mod" => [
			"color" => "§l§d",
			"permissions" => [
				"pocketmine.command.me",
				"genesis.kit.saturn",
				"genesis.kit.jupiter",
				"genesis.kit.mars",
				"genesis.kit.mercury",
				"genesis.kit.member",
				"genesis.kit.earth",
			],
			"inheritance" => null,
		],
		"SrMod" => [
			"color" => "§l§5",
			"permissions" => [
				"pocketmine.command.me",
				"genesis.kit.saturn",
				"genesis.kit.jupiter",
				"genesis.kit.mars",
				"genesis.kit.mercury",
				"genesis.kit.member",
				"genesis.kit.earth",
			],
			"inheritance" => null,
		],
		"Admin" => [
			"color" => "§l§c",
			"permissions" => [
				"pocketmine.command.me",
				"genesis.kit.saturn",
				"genesis.kit.jupiter",
				"genesis.kit.mars",
				"genesis.kit.mercury",
				"genesis.kit.member",
				"genesis.kit.earth",
			],
			"inheritance" => null,
		],
		"SrAdmin" => [
			"color" => "§l§4",
			"permissions" => [
				"pocketmine.command.me",
				"genesis.kit.saturn",
				"genesis.kit.jupiter",
				"genesis.kit.mars",
				"genesis.kit.mercury",
				"genesis.kit.member",
				"genesis.kit.earth",
			],
			"inheritance" => null,
		],
		"Management" => [
			"color" => "§l§1",
			"permissions" => [
				"pocketmine.command.me",
				"genesis.kit.saturn",
				"genesis.kit.jupiter",
				"genesis.kit.mars",
				"genesis.kit.mercury",
				"genesis.kit.member",
				"genesis.kit.earth",
			],
			"inheritance" => null,
		],
		"Owner" => [
			"color" => "§l§4",
			"permissions" => [
				"pocketmine.command.me",
				"genesis.kit.saturn",
				"genesis.kit.jupiter",
				"genesis.kit.mars",
				"genesis.kit.mercury",
				"genesis.kit.member",
				"genesis.kit.earth",
			],
			"inheritance" => null,
		],
		"Executive" => [
			"color" => "§l§d",
			"permissions" =>[
				"genesis.kit.saturn",
				"genesis.kit.jupiter",
				"genesis.kit.mars",
				"genesis.kit.mercury",
				"genesis.kit.member",
				"genesis.kit.earth",
			],
			"inheritance" => "Member",
		]
	];
}