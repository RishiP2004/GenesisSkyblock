<?php

namespace sb\inventory;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use muqsit\invmenu\type\InvMenuType;
use muqsit\invmenu\type\InvMenuTypeIds;

use pocketmine\block\Block;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use sb\item\CustomItem;
use sb\item\enchantment\CustomEnchantment;
use sb\item\enchantment\CustomEnchantments;
use sb\item\utils\RarityType;
use sb\player\CoreUser;

class CERarityInventory extends BasicInventory {
	public readonly InvMenuType $type;

	private array $cachedSlot;

	public function __construct(private Player $player, private RarityType $rarityType) {
		parent::__construct(InvMenuTypeIds::TYPE_CHEST);
	}

	public function getRarityType() : RarityType {
		return $this->rarityType;
	}

	public function createInventory(): void {
		$this->setName($this->getRarityType()->getCustomName() . " Enchants");
		$items = [];
		$this->cachedSlot = [];
		$slot = 0;
		foreach(CustomEnchantments::getAllForRarity($this->getRarityType()) as $enchantId) {
			$enchant = EnchantmentIdMap::getInstance()->fromId($enchantId);
			$book = VanillaItems::BOOK()->setLore(["", "§7Click to view info about this enchant"]);
			$book->setCustomName(RarityType::fromId($enchant->getRarity())->getColor() . $enchant->getName());
			CustomItem::applyDisplayEnchant($book);
			$items[] = $book;
			$this->cachedSlot[$slot] = $enchant;
			$slot++;
		}
		$this->getInventory()->setContents($items);
		$this->getInventory()->setItem(26, VanillaItems::ARROW()->setCustomName("§7Back"));
	}

	public function createListener(): void {
		$this->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) : void {
			$player = $transaction->getPlayer();

			if($transaction->getItemClicked()->getTypeId() == VanillaItems::ARROW()->getTypeId()) {
				new CEInventory($transaction->getPlayer());
			} else {
				/**
				 * @var CustomEnchantment $enchant
				 */
				$enchant = $this->cachedSlot[$transaction->getAction()->getSlot()];
				$player->removeCurrentWindow();
				$player->sendForm($enchant->getForm());
			}
		}));
	}
}
