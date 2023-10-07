<?php

namespace sb\player\traits;

use sb\database\Database;

use sb\player\{
	CoreUser,
	PlayerManager
};

trait PlayerCallTrait {
	public function getCoreUser(string $string, callable $callback) : void {
		if(!empty(PlayerManager::getInstance()->getAll())) {
			foreach(PlayerManager::getInstance()->getAll() as $coreUser) {
				if($coreUser instanceof CoreUser) {
					if($coreUser->getXuid() === $string or $coreUser->getName() === $string) {
						$callback($coreUser);
						return;
					}
				}
			}
		}
		$this->getDirectUser($string, $callback);
	}

	public function getDirectUser(string $string, callable $callback) : void {
		Database::get()->executeSelect("player.get", ['key' => $string], function(array $rows) use($callback) {
			if(count($rows) === 0) {
				$callback(null);
				return;
			}
			$data = $rows[0];
			$xuid = $data['xuid'];
			$coreUser = new CoreUser($xuid);

			$coreUser->load($data);
			$callback($coreUser);
		});
	}
}