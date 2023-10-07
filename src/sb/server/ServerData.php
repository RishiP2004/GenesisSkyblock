<?php

namespace sb\server;


interface ServerData {
	const PREFIX = "\n                 §r§4§lGenesis §r§8| §r§a§lNetwork \n§r";
	const SERVER_NAME = "§l§bSaturn§r";

	const VOTE_REWARD = "vote_reward";
	const VOTE_GOAL_REWARD = "votegoal_reward";
	const DAILY_REWARD = "daily_reward";
	const MONTHLY_REWARD = "monthly_reward";

	const REWARDS = [
		self::VOTE_REWARD => [
			"items" => [
				"votecrate:1",
			],
			"cmds" => [
				"addmoney {player} 5000"
			],
		],
		self::VOTE_GOAL_REWARD => [
			"items" => [
				"iron_helmet:0:1:§r§bIron Helmet:unbreaking:1",
			],
			"cmds" => []
		],
		self::DAILY_REWARD => [
			"cmds" => [
				"give {PLAYER} apple 1"
			],
			"items" => [
				"iron_helmet:0:1:§r§bIron Helmet:unbreaking:1",
			]
		],
		self::MONTHLY_REWARD => [
			"cmds" => [
				"give {PLAYER} apple 2"
			],
			"items" => [
				"iron_helmet:0:1:§r§bIron Helmet:unbreaking:1",
			]
		]
	];
}