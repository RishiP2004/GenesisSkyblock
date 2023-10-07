<?php

declare(strict_types = 1);

namespace sb\world\koth;

interface Koths {
	const PREFIX = "\n                 §r§b§lSaturn §r§8| §r§3§lKoTH \n§r";

	const KOTHS = [
		"Koth" => [
			"pos1" => "392, 110, 230, world",
			"pos2" => "403, 100, 240, world",
			"autoTime" => 60, //in min
			"winTime" => 60, //in sec
			"startCmds" => [],
			"winCmds" => [],
			"rewards" => [
				"iron_helmet:0:1:§r§bIron Helmet:unbreaking:1",
			],
		],
	];
}
