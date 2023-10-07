<?php

namespace sb\world\crates;

interface Crates {
	const CRATES = [
		"simple" => [
			"pos" => "410, 110, 238, world",
			"rewards" => [
				"banknote:25000",
				"banknote:50000",
				"xpbottle:20000",
				"sellwand",
				"cratekey:unique",
				"xpbottle:1000",
				"chunkcollector",
				//black scroll
				"permnote:fly",
				"permnote:fix",
				"permnote:heal",
				//spawners
				"DragonArmor",
				"CupidArmor"
				//tags
				//kit access
				//gkit access
			]
		],
		"unique" => [
			"pos" => "407, 110, 226, world",
			"rewards" => [
				"iron_helmet:0:1:§r§bIron Helmet:unbreaking:1",
				"banknote:25000:1",
				"banknote:50000:1",
				"xpbottle:20000:1",
				"cratekey:unique:1",
				//black scroll
				//perks
			]
		],
		"elite" => [
			"pos" => "386, 110, 238, world",
			"rewards" => [
				"iron_helmet:0:1:§r§bIron Helmet:unbreaking:1",
				"banknote:25000:1",
				"banknote:50000:1",
				"xpbottle:20000:1",
				"cratekey:unique:2",
				//black scroll
				//perks
			]
		],
		"ultimate" => [
			"pos" => "389, 110, 226, world",
			"rewards" => [
				"iron_helmet:0:1:§r§bIron Helmet:unbreaking:1",
				"banknote:25000:1",
				"banknote:50000:1",
				"xpbottle:20000:1",
				"cratekey:unique:1",
				"cratekey:op:1",
				//black scroll
				//perks
			]
		],
		"legendary" => [
			"pos" => "401, 110, 223, world",
			"rewards" => [
				"iron_helmet:0:1:§r§bIron Helmet:unbreaking:1",
				"banknote:25000:1",
				"banknote:50000:1",
				"xpbottle:20000:1",
				"cratekey:unique:1",
				"cratekey:op:1",
				//black scroll
				//perks
			]
		],
		"op" => [
			"pos" => "395, 110, 223, world",
			"rewards" => [
				"iron_helmet:0:1:§r§bIron Helmet:unbreaking:1",
				"banknote:25000:1",
				"banknote:50000:1",
				"xpbottle:20000:1",
				"cratekey:unique:1",
				"cratekey:op:1",
				"cratekey:legendary:1",
				//black scroll
				//perks
			]
		]
	];
}