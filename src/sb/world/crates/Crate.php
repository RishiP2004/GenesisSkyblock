<?php

namespace sb\world\crates;

use pocketmine\world\Position;
use sb\item\CustomItemParser;
use sb\player\CorePlayer;
use sb\utils\Reward;

class Crate {
    private array $rewards;
        
	public function __construct(private readonly string $name, private readonly Position $position){}

	public function getName() : string {
		return $this->name;
	}

	public function getPosition(): Position{
		return $this->position;
	}

	public function getRewards() : array {
        $rewards = [];
        
		foreach(Crates::CRATES as $name => $data) {
            foreach($data["rewards"] as $item) {
				$parsed = CustomItemParser::getInstance()->parse($item);
                
				if(!is_null($parsed)) {
					$reward = new Reward(($parsed), function(CorePlayer $player) use($parsed) {
						$player->getInventory()->addItem($parsed);
					}, 5); //todo: how tf I gonna do custom chances
					$rewards[$name] = $reward;
				}
			}
        }
        return $rewards;
	}

	public function getReward(int $loop = 0) : Reward {
		$chance = mt_rand(0, 100);
		$reward = $this->rewards[array_rand($this->rewards)];

		if($loop >= 10) return $reward;
		if($reward->getChance() <= $chance) return $this->getReward($loop + 1);
		return $reward;
	}

	//ik, ik
	public function getColouredName() : string {
		return match(strtolower($this->getName())) {
			'simple' => "§l§7Simple §fCrate",
			'unique' => "§l§aUnique §fCrate",
			'elite' => "§l§bElite §fCrate",
			'ultimate' => "§l§eUltimate §fCrate",
			'legendary' => "§l§6Legendary §fCrate",
			'op' => "§l§4OP §fCrate",
		};
	}
}