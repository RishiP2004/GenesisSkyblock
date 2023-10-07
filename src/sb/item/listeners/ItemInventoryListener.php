<?php

namespace sb\item\listeners;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use sb\player\CorePlayer;

interface ItemInventoryListener {
	public function onInventoryListen(CorePlayer $player, Item $item, Item $otherItem, SlotChangeAction $action, SlotChangeAction $otherAction, InventoryTransactionEvent $event) : void;
}