<?php

declare(strict_types = 1);

namespace sb\player\rank;

use pocketmine\permission\Permission;
use sb\player\CorePlayer;

class Rank {
    public function __construct(
		private string $name,
		private string $color,
		private array $permissions,
		private ?Rank $inheritance,
	) {}

    public final function getName() : string {
        return $this->name;
    }

	public final function getColor() : string {
    	return $this->color;
	}

	public function getChatFormatFor(CorePlayer $from, string $message, array $args = []) : string {
		if($from->getCoreUser()->hasIsland()) {
			return "§8[§f" . $from->getIslandName() . "§8] §r§f§l«§r§l" . $this->getColor() . $this->getName() . "§r§l§f» §r" . $from->getName() . ": " . $message;
		} else {
			return "§r§f§l«§r§l" . $this->getColor() . $this->getName() . "§r§l§f» §r" . $from->getName() . ": " . $message;
		}
	}

	public function getNameTagFormatFor(CorePlayer $player) : string {
		if($player->getCoreUser()->hasIsland()) {
			return "§8[§7". $player->getIslandName() . "§8] §r§f§l«§r§l" . $this->getColor() . $this->getName() . "§r§l§f» §r\n" . " §r§f" . $player->getName() . " " ;
		} else {
			return "§r§f§l«§r§l" . $this->getColor() . $this->getName() . "§r§l§f» §r" . $player->getName() . " " ;
		}
	}

	public function getPermissions() : array {
		$permissions = [];

		$parentRank = $this->getInheritance();

		if($parentRank instanceof Rank) {
			foreach($parentRank->getPermissions() as $parentPermission) {
				$permissions[] = $parentPermission;
			}
		}
		foreach($this->permissions as $permission) {
			$permissions[] = $permission;
		}
		return array_unique($permissions, SORT_STRING);
	}

    public final function getInheritance() : ?Rank {
    	return $this->inheritance;
	}

    public function hasPermission(Permission|string $permission){
		$perm = null;

		if($permission instanceof Permission) {
			$perm = $permission->getName();
		} else {
			$perm = $permission;
		}

		return in_array($perm, $this->getPermissions());
    }
}