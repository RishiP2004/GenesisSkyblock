<?php

namespace sb\world\prosperity;

use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\world\Position;
use sb\Skyblock;

class ProsperityHandler implements Prosperities {
	private static array $prosperities = [];

	public function __construct() {
		foreach(self::PROSPERITIES as $name => $data) {
			$pos1Ex = explode(", ", $data["pos1"]);
			$pos1 = new Position($pos1Ex[0], $pos1Ex[1], $pos1Ex[2], Server::getInstance()->getWorldManager()->getWorldByName($pos1Ex[3]));
			$pos2Ex = explode(", ", $data["pos2"]);
			$pos2 = new Position($pos2Ex[0], $pos2Ex[1], $pos2Ex[2], Server::getInstance()->getWorldManager()->getWorldByName($pos2Ex[3]));

			$prosperity = new Prosperity(
				$name,
				$pos1,
				$pos2,
				$data["tick"],
				$data["money"],
				$data["xp"]
			);
			self::init($prosperity);
		}
		Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(): void {
			foreach(self::getAll() as $prosperity) {
				$prosperity->tick();
			}
		}), 10);
	}

	public static function init(Prosperity $prosperity): void {
		self::$prosperities[$prosperity->getName()] = $prosperity;
	}

	public static function getAll() : array {
		return self::$prosperities;
	}

	public static function get(string $name) : ?Prosperity {
		return self::$prosperities[$name] ?? null;
	}
}