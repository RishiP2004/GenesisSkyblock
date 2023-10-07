<?php

namespace sb\world\koth;

use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\world\Position;
use sb\item\CustomItemParser;
use sb\Skyblock;

class KothHandler implements Koths {
	public static int $runs = 0;
	private static array $koths = [];

	public function __construct() {
		foreach(self::KOTHS as $name => $data) {
			$pos1Ex = explode(", ", $data["pos1"]);
			$pos1 = new Position($pos1Ex[0], $pos1Ex[1], $pos1Ex[2], Server::getInstance()->getWorldManager()->getWorldByName($pos1Ex[3]));
			$pos2Ex = explode(", ", $data["pos2"]);
			$pos2 = new Position($pos2Ex[0], $pos2Ex[1], $pos2Ex[2], Server::getInstance()->getWorldManager()->getWorldByName($pos2Ex[3]));

			$rewards = [];
			foreach($data["rewards"] as $item) {
				$rewards[] = CustomItemParser::getInstance()->parse($item);
			}
			$koth = new Koth(
				$name,
				$pos1,
				$pos2,
				$data["autoTime"],
				$data["winTime"],
				$data["winCmds"],
				$rewards
			);
			self::init($koth);
		}
		Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(): void {
			foreach(self::getAll() as $koth) {
				if(!$koth->isRunning()) continue;
				$koth->tick();
			}
		}), 10);
	}

	public static function tick() : void {
		self::$runs++;

		foreach(self::getAll() as $koth) {
			if(!$koth->isRunning() and self::$runs == $koth->getAutoTime()) $koth->start();
		}
	}

	public static function init(Koth $koth): void {
		self::$koths[strtolower($koth->getName())] = $koth;
	}

	public static function getAll() : array {
		return self::$koths;
	}

	public static function get(string $name) : ?Koth {
		return self::$koths[strtolower($name)] ?? null;
	}
}