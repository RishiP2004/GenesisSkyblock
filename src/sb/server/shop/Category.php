<?php

namespace sb\server\shop;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sb\server\shop\entry\ShopEntry;

class Category {
	public function __construct(private readonly string $name, private readonly Item $displayItem, private array $shopEntries) {
        foreach($shopEntries as $entry) {
            $entries[$entry->getName()] = $entry;
		}
        $this->shopEntries = $entries;
	}

	public function getName() : string {
		return $this->name;
	}

	public function getDisplayItem() : Item {
		return $this->displayItem;
	}

	public function getShopEntries() : array {
		return $this->shopEntries;
	}

	public function getShopEntriesChunked() : ?array {
		$i = $this->shopEntries;
		return array_chunk($i, 18, true);
	}

	public function getShopEntry(string $name): ?ShopEntry {
		return $this->shopEntries[$name] ?? null;
	}

	public function send(Player $player, int $page = 0, bool $backButton = true) : void {
		$chest = InvMenu::create(InvMenu::TYPE_CHEST)->setName("Shop " .$this->getName());
		$chest->setListener(function(InvMenuTransaction $transaction) use ($page, $backButton) : InvMenuTransactionResult {
			$player = $transaction->getPlayer();
			$itemClicked = $transaction->getItemClicked();
			$itemCW = $transaction->getItemClickedWith();

			if($itemCW->getTypeId() === 0) $player->getCursorInventory()->clearAll();

			if(($iname = TextFormat::clean($itemClicked->getCustomName())) === "Next Page" || $iname === "Previous Page") {
				return $transaction->discard()->then(function(Player $player) use($iname, $page, $backButton) : void{
					$this->send($player,$iname === "Next Page" ? $page + 1 : $page - 1, $backButton);
				});
			} else if($iname === "Back") {
				return $transaction->discard()->then(function(Player $player) : void{
					ShopHandler::sendMenu($player);
				});
			}else{
				$player->removeCurrentWindow();

				return $transaction->discard()->then(function(Player $player) use ($itemClicked) : void{
                    if(is_null($itemClicked)) return;
					$this->getShopEntry($itemClicked->hasCustomName() ? $itemClicked->getCustomName() : $itemClicked->getName())->sendFinal($player);
				});
			}
		});
		$data = $this->getShopEntriesChunked();
		$content = [];

		foreach($data[$page] as $index => $i) {
			$fac = $i->getItem();
			//todo: check for null sell/buy
			$s = $i->getSellPrice();
			if(is_null($i->getSellPrice())) $s = "Unsellable";
			$b = $i->getBuyPrice();
			if(is_null($i->getBuyPrice())) $b = "Unbuyable";

			$fac->setLore(["", "§r§cBuy: " . $b, "§r§aSell: " . $s]);
			if($fac->getName() !== $i->getName()) $fac->setCustomName("§r§f".$fac->getName());
			$content[] = $fac;
		}
		$chest->getInventory()->setContents($content);

		if(isset($data[$page + 1])){
			$chest->getInventory()->setItem(26, VanillaItems::DYE()->setColor(DyeColor::GREEN())->setCustomName("§r§aNext Page"));
		}
		if(isset($data[$page - 1])){
			$chest->getInventory()->setItem(18, VanillaItems::DYE()->setColor(DyeColor::RED())->setCustomName("§r§cPrevious Page"));
		}
		if($backButton) $chest->getInventory()->setItem(22, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem()->setCustomName("§rBack"));
		$chest->send($player);
	}
}