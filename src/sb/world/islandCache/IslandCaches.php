<?php

namespace sb\world\islandCache;

interface IslandCaches {
	#const PREFIX = "*";

	const SPAWN_TIME = 40; //minutes
	const DESPAWN_TIME = 10; //minutes

	const AREA = [
		"minX" => 240,
		"maxX" => 600,
		"minZ" => 240,
		"maxZ" => 600,
		"y" => 140
	];

	const ITEMS = [
		"iron_helmet:0:1:§r§bIron Helmet:unbreaking:1"
	];
}