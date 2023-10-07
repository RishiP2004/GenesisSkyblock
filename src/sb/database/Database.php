<?php

declare(strict_types = 1);

namespace sb\database;

use sb\Skyblock;

use pocketmine\Server;

use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class Database {
	private static DataConnector $database;

	public static function initialize() : void {
		try {
			self::$database = libasynql::create(Skyblock::getInstance(), Skyblock::getInstance()->getConfig()->get("database"), [
				"sqlite" => "db/queries.sql"
			]);
		} catch(\Exception $exception) {
			Server::getInstance()->getLogger()->error(Skyblock::ERROR_PREFIX . "Core Database connection failed: " . $exception->getMessage());
			Server::getInstance()->shutdown();
		}
		self::get()->executeGeneric("coinflips.init");
		self::get()->executeGeneric("server.init");
		
	}

	public static function get() : DataConnector {
		return self::$database;
	}
}