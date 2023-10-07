<?php

namespace sb\islands\utils;

interface IslandStats {
	const FISH_COUNT = "fish_count";
	const BLOCKS_MINED = "blocks_mined";
	const MOBS_KILLED_AFK = "mobs_killed_afk";
	const MOBS_KILLED_MANUALLY = "mobs_killed_manually";
	const CROPS_HARVESTED = "crops_harvested";

	const XP = [
		self::FISH_COUNT => 8,
		self::BLOCKS_MINED => 2,
		self::MOBS_KILLED_AFK => 2,
		self::MOBS_KILLED_MANUALLY => 6,
		self::CROPS_HARVESTED => 4,
	];
}