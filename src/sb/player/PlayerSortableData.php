<?php

namespace sb\player;

use sb\database\Database;

final class PlayerSortableData {
	public static function getAllSortableData(callable $callback) : void {
		Database::get()->executeSelect("player.allSortable", [], function(array $rows) use($callback) {
			$arr = [];

			foreach($rows as [
					"username" => $name,
					"money" => $money,
					"jackpotWins" => $jackpotWins,
					"jackpotEarnings" => $jackpotEarnings,
			]) {
				$arr = [
					$name => [$money, $jackpotWins, $jackpotEarnings]
				];
			}
			$callback($arr);
		});
	}

	public static function getTopMoney(int $pageSize, int $page, callable $callback) {
		self::getAllSortableData(function($data) use($pageSize, $page, $callback) {
			uasort($data, function ($a, $b) {
				return $b[0] - $a[0];
			});
			$money = array_map(function ($data) { return $data[0]; }, $data);
			$money = array_chunk($money, $pageSize, true); //DEFAULT SIZE SHOWN IS 5
			$page = min(count($money), max(1, $page));

			$callback($money[$page - 1] ?? []);
		});
	}
}