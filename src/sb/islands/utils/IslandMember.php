<?php

namespace sb\islands\utils;

use pocketmine\player\Player;
use pocketmine\Server;

class IslandMember {
    public function __construct(private readonly string $name, private readonly string $role) {}

    public function getName() : string {
        return $this->name;
    }

    public function getRole() : string {
        return $this->role;
    }

    public function getPlayer() : ?Player {
        return Server::getInstance()->getPlayerExact($this->name);
    }

    public static function unSerialize(string $to) : IslandMember {
        $to = unserialize($to);
        return new IslandMember($to["name"], $to["role"]);
    }

    public function serialize() : string {
        return serialize([
            "name" => $this->name,
            "role" => $this->role
        ]);
    }
}