<?php

namespace sb\islands\utils;

use pocketmine\math\Vector3;

class IslandGenerators {

    public const GENERATOR_WORLD = [
        self::GEN_BASIC => "Original",
		self::GEN_SNOW => "Snow",
		self::GEN_SAVANNA => "Savanna",
		self::GEN_DESERT => "Desert",
		self::GEN_FOREST => "Forest",
		self::GEN_ORIENTAL => "Oriental",
		self::GEN_BADLANDS => "BadLands"
    ];

	public static function getDefaultSpawn(string $type) : Vector3 {
		return match ($type) {
			self::GEN_BASIC => new Vector3(-35201.00, 125.00, -19199.00),
			self::GEN_SNOW => new Vector3(-35201.00, 125.00, -19199.00),
			self::GEN_SAVANNA => new Vector3(-35201.00, 125.00, -19199.00),
			self::GEN_DESERT => new Vector3(-35201.00, 125.00, -19199.00),
			self::GEN_FOREST => new Vector3(-35201.00, 125.00, -19199.00),
			self::GEN_ORIENTAL => new Vector3(-35201.00, 125.00, -19199.00),
			self::GEN_BADLANDS => new Vector3(-35201.00, 125.00, -19199.00)
		};
	}

    public const GEN_BASIC = "Basic";
	public const GEN_SNOW = "Snow";
	public const GEN_SAVANNA = "Savanna";
	public const GEN_DESERT = "Desert";
	public const GEN_FOREST = "Forest";
	public const GEN_ORIENTAL = "Oriental";
	public const GEN_BADLANDS = "BadLands";
}