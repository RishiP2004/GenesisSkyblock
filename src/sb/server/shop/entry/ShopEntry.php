<?php

declare(strict_types = 1);

namespace sb\server\shop\entry;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\element\Label;
use pocketmine\item\Item;
use pocketmine\player\Player;
use sb\item\CustomItemParser;
use sb\player\CorePlayer;
use sb\Skyblock;

class ShopEntry extends BaseEntry {
	public function __construct(
		public string         $name,
		public Item           $item,
		private readonly ?int $buyPrice = null,
		private readonly ?int $sellPrice = null
	) {
		parent::__construct($name, $item);
	}

	public function getBuyPrice(): ?int {
		return $this->buyPrice;
	}

	public function getSellPrice(): ?int {
		return $this->sellPrice;
	}

	public function sendFinal(CorePlayer $player) : void {
		$options = [];
		$buyp = $this->getBuyPrice();

		if(is_numeric($buyp)){
			$options[] = "§cBuy";
		}
		$sellp = $this->getSellPrice();

		if(is_numeric($sellp)){
			$options[] = "§aSell";
		}

		$player->sendForm(new CustomForm(
			$this->getName(),
			[
				new Dropdown("Buy_Or_Sell", "Buy or Sell?", $options),
			],
			function(Player $submitter, CustomFormResponse $response) : void {
				$option = $response->getInt("Buy_Or_Sell");

				if($option == 0 && !is_null($this->getBuyPrice())) {
					$this->buyForm($submitter);
				} else if(!is_null($this->getSellPrice())) {
					$this->sellForm($submitter);
				}
			}
		));
	}

	public function buyForm(CorePlayer $player) : void {
		$player->sendForm(new CustomForm(
			$this->getName(),
			[
				new Label("Label", "You're about to buy for §a$" . $this->getBuyPrice() . "\n§rType the quantity that you want to buy"),
				new Input("Amount", "Amount")
			],
			function(Player $submitter, CustomFormResponse $response) : void {
				$amount = $response->getString("Amount");
				if(is_numeric($amount) && ((int)$amount) > 0 && ((int) $amount) < $this->getItem()->getMaxStackSize() * 36) {
					$this->buy($submitter, (int) $amount);
				}else{
					$submitter->sendMessage(Skyblock::ERROR_PREFIX . "Invalid amount!");
				}
			}
		));
	}

	public function sellForm(CorePlayer $player) : void {
		$count = 0;

		foreach ($player->getInventory()->getContents() as $item){
			if($item->getTypeId() === $this->getItem()->getTypeId() && !CustomItemParser::getInstance()->isCustom($item)) {
				$count += $item->getCount();
			}
		}
		$totalVal = $this->getSellPrice() * $count;

		$player->sendForm(new CustomForm(
			$this->getName(),
			[
				new Label("Label", "You're about to sell for §a$" . $this->getBuyPrice() . "\n§rQuantity item that you have: §4" . $count . "\n§rTotal price you'll get (if sell all): §a" . $totalVal . "\n§rType the quantity that you want to sell"),
				new Input("Amount", "Amount")
			],
			function(Player $submitter, CustomFormResponse $response) use($count) : void {
				$amount = $response->getString("Amount");

				if((int) $amount > $count) {
					$submitter->sendMessage(Skyblock::ERROR_PREFIX . "You do not have that many of " . $this->getName());
					return;
				}
				if(is_numeric($amount) && ((int)$amount) > 0) {
					$this->sell($submitter, (int) $amount);
				}else{
					$submitter->sendMessage(Skyblock::ERROR_PREFIX . "Invalid amount!");
				}
			}
		));
	}

	public function buy(CorePlayer $player, int $amount = 1) : void {
		$buyp = $this->getBuyPrice();
		$item = $this->getItem()->setCount($amount);

		if($player->getCoreUser()->getMoney() < $buyp * $amount){
			$player->sendMessage(Skyblock::ERROR_PREFIX . "You do not have enough money to buy this item.");
		}else{
			if($player->getInventory()->canAddItem($item)){
				$item->setLore([""]);
				$player->getCoreUser()->reduceMoney($buyp * $amount);
				$player->getInventory()->addItem($item);
				$player->sendMessage(Skyblock::PREFIX . "Bought {$this->getName()} for $" . $buyp * $amount);
			}else{
				$player->sendMessage(Skyblock::ERROR_PREFIX . "You do not have enough space in your inventory to buy this item.");
			}
		}
	}

	public function sell(CorePlayer $player, int $amount = 1) : void {
		$sellp = $this->getSellPrice();
		$item = $this->getItem()->setCount($amount);

		if($player->getInventory()->contains($item)) {
			if(CustomItemParser::getInstance()->isCustom($item)) return;

			$player->getInventory()->removeItem($item);
			$player->getCoreUser()->addMoney($amt = $sellp * $amount);
			$player->sendMessage(Skyblock::PREFIX . "Sold {$this->getName()} for $" . $amt);
		} else {
			$player->sendMessage(Skyblock::ERROR_PREFIX . "You no longer have this item!");
		}
	}
}