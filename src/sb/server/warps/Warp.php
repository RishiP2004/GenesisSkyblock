<?php
namespace sb\server\warps;

use pocketmine\item\Item;
use pocketmine\world\Position;
use pocketmine\world\World;

class Warp{
	public function __construct(private readonly string $name, private readonly Item $texture, private readonly Position $position){

	}

	public function getName(): string{
		return $this->name;
	}


	public function getTexture(): Item{
		return $this->texture;
	}

	public function getPosition(): Position{
		return $this->position;
	}

	public function getWorld(): World{
		return $this->position->getWorld();
	}

	public function getNearbyPlayers(): array{
		$players = [];
		foreach ($this->getWorld()->getPlayers() as $player){
			if(!$player->getLocation()->distance($this->position) > 10) continue;
			$players[] = $player;
		}
		return $players;
	}
}