<?php

namespace sb\player;

/**
 * This interface is used to store player data.
 * We hold generic data here, such as banned commands, max balance, max level, and max xp.
 */
interface PlayerData {
	const BANNED_COMMANDS = [
		"me",
		"ver",
		"tpa",
		"spawn",
		"shop",
		"ah",
		"is",
		"warp"
	];

	const PLAYER_MAX_BALANCE = 1000000000;
	Const PLAYER_MAX_LEVEL = 100;
	const PLAYER_MAX_XP = 1000000000;
}