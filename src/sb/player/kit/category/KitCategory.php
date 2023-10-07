<?php
namespace sb\player\kit\category;

use pocketmine\item\Item;
use sb\player\kit\Kit;

class KitCategory{
	/**
	 * @param string $identifier
	 * @param Item $icon
	 * @param array $kits
	 */
	public function __construct(private readonly string $identifier, private readonly int $tier, private readonly Item $icon, private readonly array $kits = []){

	}

	public function getKit(string $name): ?Kit{
		foreach($this->kits as $kit){
			if($kit->getName() === $name){
				return $kit;
			}
		}
		return null;
	}

	public function getKitByIndex(int $index): ?Kit{
		return $this->kits[$index] ?? null;
	}

	public function getTier(): int{
		return $this->tier;
	}

	public function getIdentifier(): string{
		return $this->identifier;
	}

	public function getIcon(): Item{
		return $this->icon;
	}

	public function getKits(): array{
		return $this->kits;
	}
}