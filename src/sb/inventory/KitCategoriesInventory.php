<?php
namespace sb\inventory;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use sb\player\kit\category\GroupedCategories;
use sb\player\kit\category\KitCategory;
use sb\utils\PMUtils;

class KitCategoriesInventory extends BasicInventory {
	/** @var GroupedCategories */
	private $categories;

	/** @var Player */
	private $player;

	/** @var array */
	private $slotToCategoryMap = [];

	public function __construct(Player $player, GroupedCategories $categories) {
		$this->player = $player;
		$this->categories = $categories;

		parent::__construct(InvMenuTypeIds::TYPE_HOPPER);
		$this->createInventory();
		$this->createListener();
	}

	public function createInventory(): void {
		$this->setName(TextFormat::colorize("Choose a category..."));
		$this->getInventory()->clearAll();
		$slot = 1;

		$this->categories->foreach(function (KitCategory $category) use (&$slot) {
			$this->slotToCategoryMap[$slot] = $category;
			$this->inventory->setItem($slot, $category->getIcon());
			$slot++;
		});
	}

	public function createListener(): void {
		$this->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction): void {
			$slot = $transaction->getAction()->getSlot();
			if (!isset($this->slotToCategoryMap[$slot])) {
				return;
			}

			$category = $this->slotToCategoryMap[$slot];
			$inventory = new KitInventory($this->player, $category);
			$inventory->send($this->player);

			PMUtils::sendSound($this->player, "mob.villager.yes");
		}));
	}
}

