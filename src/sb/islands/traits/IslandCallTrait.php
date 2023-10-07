<?php

namespace sb\islands\traits;

use sb\database\Database;
use sb\database\IslandDB;
use sb\islands\Island;
use sb\islands\IslandManager;
//todo: remove this
trait IslandCallTrait {
	public function getIsland(string $string, callable $callback) : void {
		if(!empty(IslandManager::getInstance()->getIslands())) {
			foreach(IslandManager::getInstance()->getIslands() as $island) {
				if($island instanceof Island) {
					if($island->getId() === $string or $island->getName() === $string) {
						$callback($island);
						return;
					}
				}
			}
		}
		$this->getDirectIsland($string, $callback);
	}

	public function getDirectIsland(string $string, callable $callback) : void {
		IslandDB::get()->executeSelect("islands.get", ['key' => $string], function(array $rows) use($callback) {
			if(count($rows) === 0) {
				$callback(null);
				return;
			}
			$data = $rows[0];
			$id = $data['id'];
			$island = new Island($id);

			$island->load($data);
			$callback($island);
		});
	}
}