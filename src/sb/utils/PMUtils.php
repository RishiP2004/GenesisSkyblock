<?php

declare(strict_types = 1);

namespace sb\utils;

use pocketmine\permission\{
    DefaultPermissions,
    PermissionManager
};
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;

final class PMUtils  {
	public static function genValidation(int $min, int $max, bool $mustBeReal = true) : callable {
		return function(string $text) use ($min, $max, $mustBeReal) : bool {
			if ($mustBeReal && !ctype_alnum($text)) {
				return false;
			}
			$len = strlen($text);
			return $len > $min && $len < $max;
		};
	}

	public static function genIntValidation(int $min, int $max = 2147483647) : callable {
		return function(string $text) use ($min, $max) : bool {
			return is_numeric($text) && $text > $min && $text < $max;
		};
	}

	public static function sendSound(
		Player $player,
		string $sound
	): void{
		$location = $player->getLocation();
		$player->getNetworkSession()->sendDataPacket(PlaySoundPacket::create($sound, $location->x, $location->y, $location->z, 1, 1));
	}

    public static function getPocketMinePermissions() : array {
        $pmPerms = [];
        
        foreach(PermissionManager::getInstance()->getPermissions() as $permission) {
            if(strpos($permission->getName(), DefaultPermissions::ROOT_OPERATOR) !== false) {
                $pmPerms[] = $permission;
            }
        }
        return $pmPerms;
    }
}