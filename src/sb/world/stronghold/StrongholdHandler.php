<?php

namespace sb\world\koth;

use pocketmine\scheduler\ClosureTask;
use sb\command\world\stronghold\StrongholdCommand;
use sb\Skyblock;
use sb\world\WorldManager;

class StrongholdHandler implements Koths {
	private static array $strongholds = [];

	public function __construct(WorldManager $manager) {
		Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(): void {
			foreach(self::$strongholds as $stronghold) {
				$stronghold->tick();
			}
		}), 20);
		self::init(new PvpStronghold());
		$manager->registerPermissions([
			"stronghold.command" => [
				"default" => "true",
				"description" => "Stronghold command"
			]
		]);
		$manager->registerCommands("player",
			new StrongholdCommand(Skyblock::getInstance(), "stronghold", "Stronghold Command"),
		);
	}

	public static function init(Stronghold $stronghold): void {
		self::$koths[$stronghold->getName()] = $stronghold;
	}

	public static function getAll() : array {
		return self::$strongholds;
	}

	public static function get(string $name) : ?Stronghold {
		return self::$strongholds[$name] ?? null;
	}
}