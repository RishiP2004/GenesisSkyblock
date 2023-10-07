<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class SlotbotTicket extends CustomItem {
	public function getItem(): Item {
		$item = VanillaItems::PAPER();
		$item->setCustomName("§r§l§aSlotbot Ticket");
		$item->setLore([
			"§r§7Congrats! You've found a §l§aSlotbot Ticket!",
			"§r§7Go to spawn and open the slotbot menu to get cool rewards!!",
		]);

		self::addNameTag($item);
		return $item;
	}

	public function getName() : string{
		return "Slotbot Ticket";
	}

	public function getId() : string {
		return CustomItemIds::SLOTBOT_TICKET;
	}
}