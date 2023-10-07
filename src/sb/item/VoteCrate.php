<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use sb\item\listeners\ItemUseListener;
use sb\player\CorePlayer;

class VoteCrate extends CustomItem implements ItemUseListener {
	public function getName(): string {
		return "Vote Crate";
	}

	public function getItem() : Item {
		$item = VanillaBlocks::ENDER_CHEST()->asItem();

		$item->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::GREEN . "Vote Crate");
		$item->setLore([
			"§r§7A stash of overpowered goodies",
			"§r§7supplied by the Sky Gods!",
			"",
			TextFormat::RESET . TextFormat::GRAY . "Contains " . TextFormat::GREEN . "1 Vote Rarity " . TextFormat::RESET . TextFormat::GRAY . "item...",
			"§r§l§a*§r§7Rank: I",
			"§r§l§a*§r§7Seasonal Rank: I",
			"§r§l§a*§r§7Command Vouchers",
			"§r§l§a*§r§7Crate Keys/Containers",
			"§r§l§a*§r§7Rare Key All",
			"§r§l§a*§r§7Unique Spawner Case",
			"§r§l§a*§r§7Random Spawners",
			"§r§l§a*§r§7Money Pot",
			"§r§l§a*§r§7Mobcoin Finder",
			"§r§l§a*§r§7Kit Vouchers",
			"§r§l§a*§r§7Diamond Armor/Tools",
			"§r§l§a*§r§75000 - 15000 XP",
			"§r§l§a*§r§725 - 150 Mob Coins",
			"§r§l§a*§r§7$50000 - $150000"
		]);
		self::addNameTag($item);
		return $item;
	}

	public function onInteract(Item $item, CorePlayer $player, PlayerInteractEvent $event): void{
		$event->cancel();
	}

	public function onUse(Item $item, CorePlayer $player, PlayerItemUseEvent $event) : void {
		$event->cancel();
		$amount = $item->getNamedTag()->getInt($this->getName(), 0);
		$id = $item->getNamedTag()->getString(self::TAG_ID, "");

		if($amount > 0 && $id !== "") {
			$item->pop();
			$player->getInventory()->setItemInHand($item);
			$player->rollVoteCrate();
		}
	}

	public function getId() : string {
		return CustomItemIds::VOTE_CRATE;
	}
}