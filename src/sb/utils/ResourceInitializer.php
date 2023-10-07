<?php

namespace sb\utils;

use sb\Skyblock;

final class ResourceInitializer {
	public static function initialize() {
		Skyblock::getInstance()->saveDefaultConfig();

		self::createDirectory("config");
		self::createDirectory("db");
		self::createDirectory("skins");

		self::initDb("queries.sql");
		self::initDb("islandqueries.sql");
		self::initConfig("kitcategories.yml");
		self::initConfig("warps.yml");
		self::initConfig("areas.yml");
	}

	private static function createDirectory(string $subdirectory): void{
		$dataFolder = Skyblock::getInstance()->getDataFolder();
		$dir = $dataFolder . $subdirectory;

		if(!is_dir($dir)) @mkdir($dir);

	}

	public static function getPath(string $dir) : string {
		return Skyblock::getInstance()->getDataFolder() . $dir . DIRECTORY_SEPARATOR;
	}

	private static function initDb(string $fileName) : void {
		Skyblock::getInstance()->saveResource("db" . DIRECTORY_SEPARATOR . $fileName);
	}

	private static function initConfig(string $fileName) : void {
		Skyblock::getInstance()->saveResource("config" . DIRECTORY_SEPARATOR . $fileName);
	}

	private static function saveSkins() : void {
		foreach(Skyblock::getInstance()->getResources() as $resource => $splFileInfo) {
			if($splFileInfo->getExtension() == "png") {
				Skyblock::getInstance()->saveResource("skins" . DIRECTORY_SEPARATOR . $splFileInfo->getBasename());
			}
		}
	}
}