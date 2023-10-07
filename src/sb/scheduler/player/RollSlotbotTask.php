<?php

namespace sb\scheduler\player;

use pocketmine\block\Block;
use pocketmine\block\StainedGlassPane;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use sb\player\CorePlayer;
use sb\player\slotbot\SlotBotHandler;
use sb\Skyblock;
use pocketmine\inventory\Inventory;
use pocketmine\scheduler\Task;

class RollSlotbotTask extends Task {
	public const MAPPED_ROLLING_ORDERS = [
		47 => [2,11,20,29,38],
		48 => [3,12,21,30,39],
		49 => [4,13,22,31,40],
		50 => [5,14,23,32,41],
		51 => [6,15,24,33,42]
	];

	public const MAPPED_REWARD_ORDERS = [
		20, 21, 22, 23, 24
	];

	public function __construct(
		private readonly CorePlayer $player,
		private readonly Inventory  $inventory,
		private readonly array      $validSlots
	) {}

    public function onRun() : void {
		$player = $this->player;
		if ($player === null or !$player->isOnline()) {
			$this->getHandler()->cancel();
			return;
		}
		$inv = $this->inventory;
		$player->slotbotRunningTime++;

		if ($player->slotbotRunningTime > 12) {
			if (!$player->getCurrentWindow() === null) $player->removeCurrentWindow();
			$player->slotBotRunning = false;
			$player->slotbotRunningTime = 0;

			foreach ($inv->getContents() as $index => $item) {
				if($item instanceof StainedGlassPane) continue;
				if($item instanceof Block) continue;
				if($item->getTypeId() == VanillaBlocks::STAINED_GLASS()->asItem()->getTypeId())	 continue;
				if ((in_array($index, self::MAPPED_REWARD_ORDERS, true))) {

					$player->getInventory()->addItem($item);
				}
			}
			$player->sendMessage(Skyblock::PREFIX . "Received Slotbot Rewards!");
			$this->getHandler()->cancel();
			return;
		}
        foreach ($this->validSlots as $slot) {
            foreach(self::MAPPED_ROLLING_ORDERS[$slot] as $s => $mSlot) {
                $inv->setItem($mSlot, $this->generateRollingItem());
            }
        }
    }
	//todo: change when random rewards rewrite
	public function generateRollingItem() : Item {
		$type = mt_rand(1, 3);

		return SlotBotHandler::getRewards()[$type];
	}
}