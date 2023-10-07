<?php

namespace sb\islands\utils;

use sb\permission\utils\IslandPermissions;

class IslandRole {
    public function __construct(private string $name, private array $permissions, private bool $canBeDeleted = true) {}

    public function getName() : string {
        return $this->name;
    }

    public function setPermissions(array $new) : void {
        $this->permissions = $new;
    }

	public function setPermission(string $permission, bool $val) {
		if($val) {
			if(!in_array($permission, $this->permissions)) {
				$this->permissions[] = $permission;
			}
		} else {
			unset($this->permissions[array_search($permission, $this->permissions)]);
		}
	}

    public function hasPermission(string $permission) : bool {
        if (in_array(IslandPermissions::PERMISSION_ALL, $this->permissions)) {
            return true;
		}
        return in_array($permission, $this->permissions);
    }

    public function getCanBeDeleted() : bool {
        return $this->canBeDeleted;
    }

    public static function unSerialize(string $to) : IslandRole {
        $to = unserialize($to);
        return new IslandRole($to["name"], $to["permissions"], $to["canBeDeleted"]);
    }

    public function serialize() : string {
        return serialize([
            "name" => $this->name,
            "permissions" => $this->permissions,
            "canBeDeleted" => $this->canBeDeleted
        ]);
    }

}