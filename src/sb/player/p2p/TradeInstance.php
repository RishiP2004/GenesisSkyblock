<?php

declare(strict_types=1);

namespace sb\player\p2p;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use sb\player\CorePlayer;
use sb\Skyblock;

class TradeInstance {
	protected InvMenu $menu;
	protected array $playerOneSlots = [];

	protected array $playerTwoSlots = [];

	protected array $acceptSlots = [0, 27];

	private bool $done = false;
	private bool $gave = false;
	private bool $closing = false;

	private bool $closed1 = false;
	private bool $closed2 = false;

	public function __construct(private readonly CorePlayer $player1, private readonly CorePlayer $player2) {
		$this->playerOneSlots = range(1, 26);
		$this->playerTwoSlots = range(28, 53);
		$this->menu = $this->constructMenu();
		$this->menu->send($this->player1);
		$this->menu->send($this->player2);
	}

	public function getMenu() : InvMenu {
		return $this->menu;
	}

	public function constructMenu() : InvMenu {
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName($this->player1->getName() . " trading " . $this->player2->getName());

		$menu->getInventory()->setItem($this->acceptSlots[0], $this->getAcceptItem($this->player1));
		$menu->getInventory()->setItem($this->acceptSlots[1], $this->getAcceptItem($this->player2));

		$menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult {
			if($this->closing === true){
				return $transaction->discard();
			}
			$player = $transaction->getPlayer();
			$out = $transaction->getOut();
			$slot = $transaction->getAction()->getSlot();

			if($out->getNamedTag()->getString("accept", "") !== "") {
				$p = $out->getNamedTag()->getString("accept", "");

				if($p === $player->getName() && $slot === $this->acceptSlots[0]){
					$this->menu->getInventory()->setItem($this->acceptSlots[0], $this->getRejectItem($player));
					$this->done = $this->checkTrade();
				}
				if($p === $player->getName() && $slot === $this->acceptSlots[1]){
					$this->menu->getInventory()->setItem($this->acceptSlots[1], $this->getRejectItem($player));
					$this->done = $this->checkTrade();
				}
			}
			if($out->getNamedTag()->getString("reject", "") !== ""){
				$this->menu->getInventory()->setItem($slot, $this->getAcceptItem($player));
				return $transaction->discard();
			}
			if($player->getName() === $this->player1->getName()){
				if(in_array($slot, $this->playerOneSlots)){
					$this->menu->getInventory()->setItem($this->acceptSlots[0], $this->getAcceptItem($this->player1));
					$this->menu->getInventory()->setItem($this->acceptSlots[1], $this->getAcceptItem($this->player2));
					return $transaction->continue();
				}
			}
			if($player->getName() === $this->player2->getName()){
				if(in_array($slot, $this->playerTwoSlots)){
					$this->menu->getInventory()->setItem($this->acceptSlots[0], $this->getAcceptItem($this->player1));
					$this->menu->getInventory()->setItem($this->acceptSlots[1], $this->getAcceptItem($this->player2));
					return $transaction->continue();
				}
			}
			return $transaction->discard();
		});

		$menu->setInventoryCloseListener(function(CorePlayer $player) {
			if($player->getName() === $this->player1->getName()){
				$this->closed1 = true;
			} else $this->closed2 = true;

			$this->close();

			$this->closing = true;

			if($this->done === false) {
				if($this->gave === false) {
					$this->gave = true;

					foreach($this->playerOneSlots as $slot){
						if(!(($i = $this->getMenu()->getInventory()->getItem($slot))->isNull())){
							foreach ($this->player1->getInventory()->addItem($i) as $it) {
								$this->player1->getWorld()->dropItem($this->player1->getPosition()->asVector3(), $it);
							}
						}
					}

					foreach($this->playerTwoSlots as $slot){
						if(!(($i = $this->getMenu()->getInventory()->getItem($slot))->isNull())){
							foreach ($this->player2->getInventory()->addItem($i) as $it) {
								$this->player2->getWorld()->dropItem($this->player2->getPosition()->asVector3(), $it);
							}
						}
					}
				}

				$player->sendMessage(Skyblock::PREFIX . "Trade has been cancelled.");
			}
		});
		return $menu;
	}

	public function checkTrade() : bool {
		$all = true;

		if(!$this->player1->isOnline() || !$this->player2->isOnline()){
			return false;
		}
		foreach($this->acceptSlots as $slot) {
			if($this->getMenu()->getInventory()->getItem($slot)->getNamedTag()->getString("accept", "") !== "") {
				$all = false;
				break;
			}
		}
		if($all === true) {
			$this->done = true;
			$this->close();

			foreach($this->playerOneSlots as $slot) {
				if(!(($i = $this->getMenu()->getInventory()->getItem($slot))->isNull())){
					foreach ($this->player2->getInventory()->addItem($i) as $it) {
						$this->player2->getWorld()->dropItem($this->player2->getPosition()->asVector3(), $it);
					}
				}
			}

			foreach($this->playerTwoSlots as $slot) {
				if(!(($i = $this->getMenu()->getInventory()->getItem($slot))->isNull())) {
					foreach ($this->player1->getInventory()->addItem($i) as $it) {
						$this->player1->getWorld()->dropItem($this->player1->getPosition()->asVector3(), $it);
					}
				}
			}
			$this->player1->sendMessage(Skyblock::PREFIX . "Trade has been successfully done");
			$this->player2->sendMessage(Skyblock::PREFIX . "Trade has been successfully done");
			return true;
		}
		return false;
	}

	public function close() : void {
		if($this->closed1 === false){
			$this->player1->removeCurrentWindow();
		}

		if($this->closed2 === false){
			$this->player2->removeCurrentWindow();
		}
	}

	public function getAcceptItem(CorePlayer $player): Item {
		$item = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GREEN())->asItem();
		$item->setCustomName("§aClick to accept");
		$item->getNamedTag()->setString("accept", $player->getName());

		return $item;
	}

	public function getRejectItem(CorePlayer $player): Item {
		$item = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED())->asItem();
		$item->setCustomName("§aTrade accepting...");
		$item->setLore(["§7Click me to cancel the trade"]);
		$item->getNamedTag()->setString("reject", $player->getName());
		return $item;
	}
}