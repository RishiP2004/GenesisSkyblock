<?php

declare(strict_types = 1);

namespace sb\database;

use sb\Skyblock;

use pocketmine\Server;

use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class IslandDB {
	private static DataConnector $database;

	public static function initialize() : void {
		try {
			self::$database = libasynql::create(Skyblock::getInstance(), Skyblock::getInstance()->getConfig()->get("database-is"), [
				"sqlite" => "db/islandqueries.sql"
			]);
		} catch(\Exception $exception) {
			Server::getInstance()->getLogger()->error(Skyblock::ERROR_PREFIX . "Core Island Database connection failed: " . $exception->getMessage());
			Server::getInstance()->shutdown();
		}
	}

	public static function get() : DataConnector {
		return self::$database;
	}
}