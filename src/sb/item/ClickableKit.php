<?php
namespace sb\item;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat as T;
use sb\item\listeners\ItemUseListener;
use sb\player\CorePlayer;
use sb\player\kit\Kit;
use sb\player\kit\KitHandler;
use sb\utils\PMUtils;

class ClickableKit extends CustomItem implements ItemUseListener{

	public function getName(): string{
		return "Clickable_Kit";
	}

	public function getId(): string{
		return CustomItemIds::CLICKABLE_KIT;
	}

	public function getItem(Kit $kit): Item{
		$kitName = $kit->getName();
		$item = VanillaItems::BOOK();
		$customName = T::colorize("&r&e&l'{$kitName}' Kit &r&f(#0339)");
		$customLore = [
			T::colorize("&r&7Right-click to obtain the &e'{$kitName}' &r&7kit contents."),
			T::colorize("&r&7redeem now to fight alongside your allies!"),
			"",
			T::colorize("&r&c&lWarning: &r&7This voucher can only be used"),
			T::colorize("&r&7once and is not refundable if lost."),

		];
		$item->setCustomName($customName);
		$item->setLore($customLore);
		$item->setCount(1);
		$item->getNamedTag()->setString("kit", $kit->getName());


		self::addNameTag($item);
		return $item;
	}

	public function onUse(Item $item, CorePlayer $player, PlayerItemUseEvent $event): void{
		$event->cancel();
		$kitName = $item->getNamedTag()->getString("kit", "");
		if($kitName !== ""){
			$item->pop();
			$player->getInventory()->setItemInHand($item);
			PMUtils::sendSound($player, "firework.twinkle");

			$kit = KitHandler::getInstance()->getKit($kitName);
			$kit->giveItems($player);
		}
	}
}