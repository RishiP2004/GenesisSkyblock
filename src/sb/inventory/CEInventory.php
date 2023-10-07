<?php

namespace sb\inventory;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use muqsit\invmenu\type\InvMenuType;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use sb\item\utils\RarityType;

class CEInventory extends BasicInventory {
	private Player $player;

	public readonly InvMenuType $type;

	public function __construct(Player $player) {
		$this->player = $player;

		parent::__construct(InvMenuTypeIds::TYPE_CHEST);
	}

	public function createInventory(): void {
		$this->setName("Custom Enchants");

		$book = VanillaItems::BOOK()->setLore(["", "§7Click to view Custom Enchants", "§7of this rarity"]);

		$this->getInventory()->setItem(10, $book->setCustomName(RarityType::UNCOMMON()->getCustomName() . " Enchants"));
		$this->getInventory()->setItem(11, $book->setCustomName(RarityType::RARE()->getCustomName() . " Enchants"));
		$this->getInventory()->setItem(12, $book->setCustomName(RarityType::ELITE()->getCustomName() . " Enchants"));
		$this->getInventory()->setItem(13, $book->setCustomName(RarityType::MASTER()->getCustomName() . " Enchants"));
		$this->getInventory()->setItem(14, $book->setCustomName(RarityType::LEGENDARY()->getCustomName() . " Enchants"));
		//$this->getInventory()->setItem(15, $book->setCustomName(RarityType::HEROIC()->getCustomName() . " Enchants"));

		foreach($this->getInventory()->getContents(true) as $k => $v) {
			if($v->isNull()) {
				$this->getInventory()->setItem($k, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
			}
		}
	}

	public function createListener(): void {
		$this->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) : void {
			$player = $transaction->getPlayer();

			$rarity = match($transaction->getAction()->getSlot()) {
				10 => RarityType::fromString("Uncommon"),
				11 => RarityType::fromString("Rare"),
				12 => RarityType::fromString("Elite"),
				13 => RarityType::fromString("Master"),
				14 => RarityType::fromString("Legendary"),
				//15 => RarityType::fromString("Heroic"),
				default=> null
			};
			if($rarity == null) return;
			(new CERarityInventory($player, $rarity))->send($player);
		}));
	}
}
