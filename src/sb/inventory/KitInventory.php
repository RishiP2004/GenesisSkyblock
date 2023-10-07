<?php

namespace sb\inventory;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use muqsit\invmenu\type\InvMenuType;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\Block;
use pocketmine\block\StainedGlassPane;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\world\sound\PopSound;
use sb\item\CustomItems;
use sb\lang\CustomKnownTranslationFactory;
use sb\player\CorePlayer;
use sb\player\kit\category\KitCategory;
use sb\player\kit\utils\KitRelation as Relation;
use sb\utils\MathUtils;

class KitInventory extends BasicInventory {
	private KitCategory $category;
	private Player $player;
	private array $slotToKitMap = [];
	public readonly InvMenuType $type;

	/**
	 * @param Player $player
	 * @param KitCategory $category
	 */
	public function __construct(Player $player, KitCategory $category) {
		$this->player = $player;
		$this->category = $category;

		parent::__construct(InvMenuTypeIds::TYPE_CHEST);
	}

	public function createInventory(): void {
		$this->setName(TextFormat::colorize("Choose a kit..."));
		$this->getInventory()->clearAll();
		$kitsData = [];

		$slot = 0;
		foreach ($this->category->getKits() as $name => $kit) {
			/** @var CorePlayer $player*/
			$coreUser = $this->player->getCoreUser();
			$kitStatus = ($coreUser->hasKitCd($kit) ? Relation::COOLDOWN : ($coreUser->hasKit($kit) ? Relation::UNLOCKED : Relation::LOCKED));

			$kitsData[$slot++] = [$name, $kitStatus];
		}

		foreach ($kitsData as $slot => [$kitName, $status]) {
			$this->slotToKitMap[$slot] = $kitName;
			$this->inventory->setItem($slot, $this->createKitItem($kitName, $status));
		}
	}

	/**
	 * @param string $kitName
	 * @param int $status
	 * @return Item
	 */
	/**
	 * Create a kit item with advanced information and styling.
	 *
	 * @param string $kitName The name of the kit.
	 * @param int $status The status of the kit (Relation::LOCKED, Relation::UNLOCKED, or Relation::COOLDOWN).
	 * @return Item The created kit item.
	 */
	private function createKitItem(string $kitName, int $status): Item{
		$kit = $this->category->getKit($kitName);
		$item = match($status) {
			Relation::LOCKED, Relation::COOLDOWN => VanillaBlocks::STAINED_GLASS_PANE(),
			Relation::UNLOCKED => VanillaBlocks::CHEST()->asItem(),
		};
		$itemDyeColor = match ($status) {
			Relation::LOCKED => DyeColor::RED(),
			Relation::UNLOCKED => DyeColor::LIME(),
			Relation::COOLDOWN => DyeColor::YELLOW(),
		};
		if($item instanceof StainedGlassPane) $item->setColor($itemDyeColor);
		$item = $item instanceof Item ? $item : $item->asItem();

		$itemDisplayName = TextFormat::colorize("&r&3&l{$kitName} /kit");
		$locked = $this->player->hasPermission($kit->getPermission()) ? TextFormat::colorize("&r&a&lUNLOCKED") : TextFormat::colorize("&r&c&lLOCKED");
		$itemCount = count(array_merge($kit->getInventory(), $kit->getArmour()));

		$kitCooldown = $this->player->getKitCd($kit);
		$someItemsFromKit = array_map(function (Item $item): string{
			$count = $item->getCount();
			$customName = TextFormat::clean($item->hasCustomName() ? $item->getCustomName() : $item->getName());

			return "x{$count} {$customName}";
		}, array_slice(array_merge($kit->getInventory(), $kit->getArmour()), 0, 12));

		$lorePlaceholders = [
			"{itemCount}" => $itemCount,
			"{locked}" => $locked,
			"{detailed}" => implode("\n", $someItemsFromKit),
			"{time_cooldown}" => MathUtils::getFormattedTime($kitCooldown)
		];

		$lore = match ($status) {
			Relation::LOCKED, Relation::UNLOCKED => [
				"",
				"&r&3&lInformation:",
				"&r&3- &r&7Total Items: {itemCount}",
				"&r&3- &r&7Items:",
				"&r&7{detailed}",
				"&r&7and more...",
				"&d",
				"{locked}",
				'&r&7Click to select kit.'
			],
			Relation::COOLDOWN => [
				"&r&cCooldown for {time_cooldown}",
			]
		};
		$itemDisplaylore = array_map(function (string $line) use ($lorePlaceholders): string{
			return TextFormat::colorize(str_replace(array_keys($lorePlaceholders), array_values($lorePlaceholders), $line));
		}, $lore);

		$item->setCustomName($itemDisplayName);
		$item->setLore($itemDisplaylore);
		return $item;
	}




	public function createListener(): void {
		$this->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) {
			/** @var CorePlayer $player */
			$player = $transaction->getPlayer();
			$slot = $transaction->getAction()->getSlot();

			if(!isset($this->slotToKitMap[$slot])) {
				return;
			}

			$kitName = $this->slotToKitMap[$slot];
			$kit = $this->category->getKit($kitName);

			if(!$player->hasPermission($kit->getPermission())){
				$player->removeCurrentWindow();

				$player->getWorld()->addSound($player->getPosition(), new AnvilFallSound());
				$player->sendMessage(CustomKnownTranslationFactory::player_has_no_permission_kit($kitName));
				return;
			}
			if($player->hasKitCD($kit)){
				$player->removeCurrentWindow();
				$player->sendMessage(CustomKnownTranslationFactory::player_has_kit_cooldown($kitName));

				$player->getWorld()->addSound($player->getPosition(), new AnvilFallSound());
				return;
			}

			$player->getInventory()->addItem(CustomItems::CLICKABLE_KIT()->getItem($kit));
			$player->sendMessage(CustomKnownTranslationFactory::player_received_kit($kitName));

			$player->setKitCd($kit);
			$player->getWorld()->addSound($player->getLocation(), new PopSound());
		}));
	}
}
