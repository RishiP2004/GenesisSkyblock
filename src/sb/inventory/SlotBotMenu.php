<?php

namespace sb\inventory;

use muqsit\invmenu\inventory\InvMenuInventory;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use sb\item\CustomItems;
use sb\player\CorePlayer;
use sb\player\slotbot\SlotBotHandler;
use sb\Skyblock;

final class SlotBotMenu {
	/** @var array */
	public static array $items = [], $clicked = [];

	public static array $slots = [
		47, 48, 49, 50, 51
	];

	private static int $i = 0;

	public static function getSlotCountByInvSlot(int $slot) : int {
		return match ($slot) {
			48 => 2,
			49 => 3,
			50 => 4,
			51 => 5,
			default => 1
		};
	}

	public static function send(CorePlayer $player) : void {
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$inv = $menu->getInventory();
		$menu->setName("§d§lSaturn SlotBot");

		$map = [
			VanillaBlocks::BEACON()->asItem()->setCustomName("§r§7|| §fLoot Table §r§l§7||")->setLore(["§r§7See the prizes that you can win by clicking this!"]),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaItems::PAPER()->setCustomName("§r§l§6Slot Credit Shop§7")->setLore(["§r§6Slot Credits:" . $player->getCoreUser()->getSlotCredits(), "", "§r§7The Slot Credit Shop is a §ereward store", "§r§7where players can spend their credits", "§7gained from rolling the Slot bot", "", "§r§7For each ticket you roll in the Slot Bot,", "§r§7you will receive §e1 Slot Credit.", "§r§7However, you cannot have more than 250", "§r§7credits at any one time", "", "Click to open the Slot Credit Shop"]),
			//2
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			//3
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIME())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIME())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIME())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIME())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::LIME())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaItems::MAGMA_CREAM()->setCustomName("§r§7§l §cSpin §7")->setLore(["§r§7Insert a Slot Bot Ticket to Spin the Bot!"]),
			//4
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			//5
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::WHITE())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			//6
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem()->setCustomName("§r§7[§c§lEMPTY TICKET SLOT§7]")->setLore(["§r§7Please drag the §l§bTicket §r§7", "in order to replace", "§r§7this with a ticket.", "", "n§r§8(§l§c!§r§8)§7 If you don't know how to play", "click the §l§dBook and Quil §r§7or the", "§r§l§dHow To Play §r§7item §r§8(§l§c!§r§8)"]),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem()->setCount(2)->setCustomName("§r§7[§c§lEMPTY TICKET SLOT§7]")->setLore(["§r§7Please drag the §l§bTicket §r§7", "in order to replace", "§r§7this with a ticket.", "", "§r§8(§l§c!§r§8)§7 If you don't know how to play", "click the §l§dBook and Quil §r§7or the", "§r§l§dHow To Play §r§7item §r§8(§l§c!§r§8)"]),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem()->setCount(3)->setCustomName("§r§7[§c§lEMPTY TICKET SLOT§7]")->setLore(["§r§7Please drag the §l§bTicket §r§7", "in order to replace", "§r§7this with a ticket.", "", "§r§8(§l§c!§r§8)§7 If you don't know how to play", "click the §l§dBook and Quil §r§7or the", "§r§l§dHow To Play §r§7item §r§8(§l§c!§r§8)"]),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem()->setCount(4)->setCustomName("§r§7[§c§lEMPTY TICKET SLOT§7]")->setLore(["§r§7Please drag the §l§bTicket §r§7", "in order to replace", "§r§7this with a ticket.", "", "§r§8(§l§c!§r§8)§7 If you don't know how to play", "click the §l§dBook and Quil §r§7or the", "§r§l§dHow To Play §r§7item §r§8(§l§c!§r§8)"]),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem()->setCount(5)->setCustomName("§r§7[§c§lEMPTY TICKET SLOT§7]")->setLore(["§r§7Please drag the §l§bTicket §r§7", "§r§7in order to replace", "§r§7this with a ticket.", "", "§r§8(§l§c!§r§8)§7 If you don't know how to play", "click the §l§dBook and Quil §r§7or the", "§r§l§dHow To Play §r§7item §r§8(§l§c!§r§8)"]),
			VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::BLACK())->asItem(),
			VanillaItems::WRITTEN_BOOK()->setCustomName("§r§l§7|| §dHow to play §l§7||")->setLore(["§r§7Hello and welcome to the §l§aSlot Bot Machine!", "§r§7You can win cool rewards here! We wish you good luck!!", "§r§7Here are the steps to make life easier for you:", "", "§r§1. Click the §l§bPaper §r§7or the §r§l§bAdd Tickets §r§7item", "in order to make the machine start adding tickets.", "", "§r§72. Once you've at least added 1 ticket then click the §l§aGreen Dye", "§r§7or the §l§aStart Rolling§r§7 item in order to start the machine", "§r§7and claim your prizes!", "", "§r§8(§l§c!§r§8) §r§7You can check out the cool rewards you can win", "§r§7by clicking the §l§fBeacon §r§7or the §l§fLoot Table §r§7item. §8(§l§c§r§8)"]),
		];
		$slots = (count($inv->getContents(true)) - 1);
		foreach (array_reverse($map) as $slot) {
			$inv->setItem($slots, $slot);
			$slots--;
		}
		$menu->send($player);
		$menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult {
			$item = $transaction->getItemClicked();
			$inv = $transaction->getAction()->getInventory();
			/**
			 * @var CorePlayer $player
			 */
			$player = $transaction->getPlayer();
			$out = $transaction->getOut();
			$slot = $transaction->getAction()->getSlot();

			if ($player->slotBotRunning) return $transaction->discard();

			if ($item->getTypeId() == VanillaBlocks::GOLD()->asItem()->getTypeId()) {
				$player->removeCurrentWindow();
				//todo: open slot shop.
			}
			$ticket = CustomItems::SLOTBOT_TICKET()->getItem();

			if(in_array($slot, self::$slots)) {
				if($out->getTypeId() === VanillaBlocks::STAINED_GLASS()->asItem()->getTypeId()) {
					if($player->getInventory()->contains($ticket)) {
						$player->getInventory()->removeItem($ticket);
						$transaction->getAction()->getInventory()->setItem($slot, $ticket->setCount(self::getSlotCountByInvSlot($slot)));
					} else {
						$player->sendMessage(Skyblock::ERROR_PREFIX . "You don't have any slot bot tickets in your inventory");
					}
				}elseif($out->getTypeId() === $ticket->getTypeId()) {
					$item->setCount(1);
					$transaction->getAction()->getInventory()->setItem($slot, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED())->asItem()->setCount($this->getSlotCountByInvSlot($slot))->setCustomName("§r§7[§c§lEMPTY TICKET SLOT§7]")->setLore(["§r§7Please drag the §l§bTicket §r§7", "in order to replace", "§r§7this with a ticket.", "", "§r§8(§l§c!§r§8)§7 If you don't know how to play", "click the §l§dBook and Quil §r§7or the", "§r§l§dHow To Play §r§7item §r§8(§l§c!§r§8)"]));
					$player->getInventory()->canAddItem($item) ? $player->getInventory()->addItem($item) : $player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
				}
			}
			if ($item->getTypeId() == VanillaBlocks::BEACON()->asItem()->getTypeId()) {
				self::$clicked = [];
				self::$items = [];
				$menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
				$menu->setName("Slotbot rewards");
				$menu->getInventory()->setContents(SlotBotHandler::getRewards());
				$menu->send($player);
				$menu->setListener(InvMenu::readonly());
			}
			if ($item->getTypeId() == ItemTypeIds::MAGMA_CREAM) {
				$red = self::$slots;
				$list = [];
				foreach ($red as $num) {
					if ($inv->getItem($num)->getTypeId() !== VanillaBlocks::STAINED_GLASS()->asItem()->getTypeId()) $list[] = $num;
				}
				if (count($list) < 1) return $transaction->discard();
				$player->rollSlotBot($list, $inv);
			}
			return $transaction->discard();
		});
		$menu->setInventoryCloseListener(function(Player $player, InvMenuInventory $inv) {
			foreach (self::$slots as $slot) {
				if(($inv->getItem($slot)->getTypeId() === VanillaItems::PAPER()->getTypeId()) ) {
					$item = CustomItems::SLOTBOT_TICKET()->getItem();
					$player->getInventory()->canAddItem($item) ? $player->getInventory()->addItem($item) : $player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
				}
			}
		});
	}
}