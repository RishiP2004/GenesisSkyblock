<?php

namespace sb\player\kit;

use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;


abstract class Kit {
	public function __construct(public string $name, public string $permission, public int $cooldown, public int $tier, public Item $icon, public ?array $armour = [], public ?array $inventory = [], public ?array $effects = []) {
		$this->init();
	}

	abstract public function init(): void;

	public function giveItems(Player $player): void{
		$position = $player->getPosition();
		$inventory = $player->getInventory();

		foreach (array_merge($this->armour, $this->inventory) as $item) {
			if ($inventory->canAddItem($item)) {
				$inventory->addItem($item);
			} else {
				$player->getWorld()->dropItem($position, $item);
			}
		}

		$player->getNetworkSession()->sendDataPacket(PlaySoundPacket::create("random.pop", $position->x, $position->y, $position->z, 1, 1));
	}

	public function getName(): string {
		return $this->name;
	}

	public function getPermission(): string {
		return $this->permission;
	}

	public function getCooldown(): int {
		return $this->cooldown;
	}

	public function getIcon(): Item {
		return $this->icon;
	}

	public function getTier(): int {
		return $this->tier;
	}

	public function getArmour(): ?array {
		return $this->armour;
	}

	public function getInventory(): ?array {
		return $this->inventory;
	}

	public function getEffects(): ?array {
		return $this->effects;
	}
}
