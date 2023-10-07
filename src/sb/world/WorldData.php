<?php

declare(strict_types = 1);

namespace sb\world;

interface WorldData {
	const MAX_DISTANCE = 16;

	const HOLOGRAMS = [
		"Join" => [
			"pos" => "257, 112, 271, world",
			"text"=>"§r§f§l§b§f* §r§3§lSaturn §r§7(/spawn) §r§f§l§b§f* \n   §r§7This map started on §d§l12/10/2023 §r§7@ §5§l2 PM EST §r§7(Map I) \n  §r§7Jounrey around /spawn to give yourself clarity   \n  §r§7of the epic adventure §r§b§lSaturn brings you! \n \n §r§fView a full list of features in the server \n §r§b/features \n \n §r§b§lServer: §r§fgenesispvp.net \n §r§b§lDiscord: §r§7discord.gg/genesispe \n §b§lStore: §r§bbuy.genesispvp.net \n \n §r§fIf any assistance is required, you can use §r§3/discord§r§f. \n §r§b/rank - §r§fLearn more about ranks on §r§3§lSaturn. \n \n §r§b§lNITRO BOOSTING \n §r§fIf you boost the server, you will gain §aaccess to §5/kit nitro§r§f. \n §r§dNitro boosting §r§falso gives perks in discord. They can be \n §r§fviewed via §r§3discord.gg/genesispe §r§7§o#boosting\n \n §r§b§ogenesispvp,net ",
			"updateTime"=>null
		],
		"Voting" => [
			"pos" => "246, 110, 268, world",
			"text"=>"§r§3§lVoting Rewards & Features \n\n §r§fWant a chance to win exclusive bundles, custom enchants, \n §r§ftactic items, and much more! \n §r§fJoin §r§3discord.gg/genesispe §r§fand read \n §r§fthe tutorial in §r§3#information§r§f! \n \n §r§3§oplay.genesispvp.net",
			"updateTime"=>null
		],
		"Crates" => [
			"pos" => "405, 110, 252, world",
			"text"=>"§b§lCrate Zone§7 \n§7 \n§7 §7Along this path§7\n§7 You'll find an area where §3Magical §7crates belong§7\n §7By opening these crates you will find §4remarkable §7rewards\n§7 these rewards could allow you to level up faster\n §4Assisting §7you in the long run with IslandTOP!",
			"updateTime"=>null
		],
		"Welcome" => [
			"pos" => "257, 110, 245, world",
			"text"=>"§r§b§lWelcome to §uGenesis Saturn \n§r§3§lSpawn Island Information: §r§f750 x 750 \n§r§3§lIsland Size: §r§f10 Players \n§r§3§lAlly Size: §r§f0 \n§f§7Run /help for  more info!",
			"updateTime"=>null
		],
		"Prosperity" => [
			"pos" => "407, 108, 264, world",
			"text"=>"§r§b§lProsperity Zone \n§r§3§lMoney Rewards: §r§a10§3x3s \n§r§3§lXP Rewards: §r§b%15§3x3s \n§r§7Capture the zone to gain rewards",
			"updateTime"=>null
		],
		"ISHELP" => [
			"pos" => "269, 110, 245, world",
			"text"=>"§r§3§lSaturn §r§7(/spawn) \n §r§b§lSkyBlock Map #1 §r§f/spawn \n §r§fTo learn the basics of SkyBlock, use: \n §r§b§l/is help \n §f§fView a full list of features in the planet: \n §r§b§l/features",
			"updateTime"=>null
		],
		"Info" => [
			"pos" => "247, 112, 246, world",
			"text"=>"§r§3§lSkyBlock Information \n §r§fLooking to give your island a bit of an edge? \n \n Island §r§b§lLeaders §r§fand §r§b§lCoLeaders §r§fare able to purchase Island Upgrades! \n §r§fType §r§b§l/is upgrade §r§fto see your options! \n §r§fUpgrades can be purchased with §3§lIsland Points §r§fand §r§3§lUluru! \n §r§b§lIslandTOP §r§fpoints can be earned from voting and/or winning them! \n \n §r§7The §r§3§lIsland §r§fwith the §b§lMOST §r§bvalue will recieve Buycraft Credit \n§7Here are the list of our rewards \n \n §r§3§l1. §r§f80$ \n §r§b§l2. §r§f55$ \n §r§b§l3. §r§f30$ \n \n §r§o§7Rewards can be claimed via discord!",
			"updateTime"=>null
		]
	];
}
