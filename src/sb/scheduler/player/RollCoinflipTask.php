<?php

namespace sb\scheduler\player;

use muqsit\invmenu\InvMenu;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\Wool;
use pocketmine\item\Item;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use sb\player\p2p\CoinflipHandler;
use sb\player\CorePlayer;
use sb\Skyblock;
use sb\player\p2p\Coinflip;

class RollCoinflipTask extends Task {

	protected int $runner = 0;
	protected int $random = 0;

	protected ?Item $winner = null;

	public function __construct(
		protected InvMenu $menu,
		protected CorePlayer $wagerer,
		protected array $colors,
		protected Coinflip $coinflip
	){
		$coinflipOwner = $this->coinflip->getPlayer();

		$head = VanillaBlocks::MOB_HEAD()
			->setMobHeadType(MobHeadType::PLAYER())
			->asItem()
			->setCustomName("§r§e".$coinflipOwner);

		$head->setNamedTag($head->getNamedTag()->setByte("isEnded", 0));

		$this->menu->getInventory()->setItem(0, $head);
		$this->menu->getInventory()->setItem(1, $this->colors[0]);

		$this->menu->send($this->wagerer);

		$coinflipOwner = $this->wagerer->getServer()->getPlayerExact($coinflipOwner);
		if ($coinflipOwner) $this->menu->send($coinflipOwner);
		$this->random = mt_rand(4, 9);
	}

	public function onRun() : void{
		if ($this->runner === $this->random) {
			$this->endCoinflip();
			return;
		}

		$newItem = array_rand($this->colors);

		$this->menu->getInventory()->setItem(2, $this->colors[$newItem]);

		$this->winner = $this->colors[$newItem];

		++$this->runner;
	}

	private function endCoinflip() : void{
		$wagerer = $this->wagerer;

		$inv = $this->menu->getInventory();

		$item = $inv->getItem(0);

		$item->setNamedTag($item->getNamedTag()->setByte("isEnded", 1));

		$inv->setItem(0, $item);

		$winner = $inv->getItem(2);

		$block = $winner->getBlock();
		$amount = $this->coinflip->getAmount() * 2;

		$winnerName = "";
		if ($block instanceof Wool) {
			if ($this->coinflip->getColor() === $block->getColor()->name()) {
				$wagerer->sendMessage(Skyblock::PREFIX . "You lost the coinflip against {$this->coinflip->getPlayer()} and lost $".$amount);

				$player = $wagerer->getServer()->getPlayerExact($this->coinflip->getPlayer());

				$player?->sendMessage(Skyblock::PREFIX ."You won the coinflip against {$wagerer->getName()} and got $".$amount);

				$winnerName = $this->coinflip->getPlayer();
			} else {
				$wagerer->sendMessage(Skyblock::PREFIX ."You won the coinflip against {$this->coinflip->getPlayer()} and got $".$amount);

				$player = $wagerer->getServer()->getPlayerExact($this->coinflip->getPlayer());

				$player?->sendMessage(Skyblock::PREFIX . "You lost the coinflip against {$wagerer->getName()} and lost $".$amount);

				$winnerName = $wagerer->getName();
			}
		}
		$this->menu->onClose($wagerer);

		Server::getInstance()->getPlayerExact($winnerName)->getCoreUser()->addMoney($amount);
		/**@var CorePlayer $player */
		$player = $wagerer->getServer()->getPlayerExact($this->coinflip->getPlayer());

		CoinflipHandler::remove($player);

		$this->getHandler()->cancel();
	}
}