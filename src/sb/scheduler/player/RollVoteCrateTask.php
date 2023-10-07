<?php

declare(strict_types = 1);

namespace sb\scheduler\player;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\world\sound\XpCollectSound;
use pocketmine\world\sound\XpLevelUpSound;

class RollVoteCrateTask extends Task {
	/** @var InvMenu */
	private InvMenu $invMenu;

	/** @var Player */
	private Player $player;

	/** @var Item[] */
	private array $rewards = [];

	/** @var int */
	private int $runs = 0;

	/**
	 * @param Player $player
	 * @param array $rewards
	 */
	public function __construct(Player $player, array $rewards)
	{
		$this->player = $player;
		$this->rewards = $rewards;

		if(empty($rewards)) $this->getHandler()->cancel();

		$inital = $inital[0] ?? VanillaItems::STICK()->setCount(1);

		$menu = InvMenu::create(InvMenu::TYPE_HOPPER);
		$menu->setName("Vote Crate");
		$menu->getInventory()->setItem(0, VanillaBlocks::BEDROCK()->asItem()->setCount(5)->setCustomName(" "));
		$menu->getInventory()->setItem(2, $inital);
		$menu->getInventory()->setItem(4, VanillaBlocks::BEDROCK()->asItem()->setCount(5)->setCustomName(" "));

		$menu->setInventoryCloseListener(function (Player $player, Inventory $inventory) use($rewards) : void {
			if($this->runs <= 100) {
				$player->getInventory()->addItem($rewards[array_rand($rewards)]);
				$player->voteCrateRunning = false;
				$this->getHandler()->cancel();
			}
		});

		$menu->setListener(function (InvMenuTransaction $transaction) : InvMenuTransactionResult {
			return $transaction->discard();
		});

		$menu->send($player);

		$this->invMenu = $menu;
	}

	public function onRun(): void {
		if($this->runs % 2 === 0)  $this->player->getWorld()->addSound($this->player->getPosition(), new XpCollectSound());

		if($this->runs % 5 === 0) {
			$this->invMenu->getInventory()->setItem(2, $this->rewards[array_rand($this->rewards)]);
		}

		if($this->runs % 20 === 0 && $this->invMenu->getInventory()->getItem(0)->getCount() > 1) {
			$this->invMenu->getInventory()->setItem(0, $this->invMenu->getInventory()->getItem(0)->setCount($this->invMenu->getInventory()->getItem(0)->getCount() - 1));
			$this->invMenu->getInventory()->setItem(4, $this->invMenu->getInventory()->getItem(4)->setCount($this->invMenu->getInventory()->getItem(4)->getCount() - 1));
		}

		$this->runs++;

		if($this->runs >= 100) {
			$this->player->getInventory()->addItem($this->invMenu->getInventory()->getItem(2));
			InvMenuHandler::getPlayerManager()->get($this->player)->removeCurrentMenu();
			$this->player->voteCrateRunning = false;
			$this->player->getWorld()->addSound($this->player->getPosition()->asVector3(), new XpLevelUpSound(30));
			$this->getHandler()->cancel();
		}
	}
}