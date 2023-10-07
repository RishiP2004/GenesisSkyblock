<?php

namespace sb\server\jackpot;

use sb\player\PlayerSortableData;

final class JackpotSortableData {
	public static function getTopWins(int $pageSize, int $page, callable $callback) : void {
		PlayerSortableData::getAllSortableData(function($data) use($pageSize, $page, $callback) {
			uasort($data, function ($a, $b) {
				return $b[1] - $a[1];
			});
			$wins = array_map(function ($data) { return $data[0]; }, $data);
			$wins = array_chunk($wins, $pageSize, true); //DEFAULT SIZE SHOWN IS 5
			$page = min(count($wins), max(1, $page));

			$callback($wins[$page - 1] ?? []);
		});
	}

	public static function getTopEarnings(int $pageSize, int $page, callable $callback) : void {
		PlayerSortableData::getAllSortableData(function($data) use($pageSize, $page, $callback) {
			uasort($data, function ($a, $b) {
				return $b[2] - $a[2];
			});
			$earnings = array_map(function ($data) { return $data[0]; }, $data);
			$earnings = array_chunk($earnings, $pageSize, true); //DEFAULT SIZE SHOWN IS 5
			$page = min(count($earnings), max(1, $page));

			$callback($earnings[$page - 1] ?? []);
		});
	}

}