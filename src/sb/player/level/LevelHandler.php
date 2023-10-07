<?php

namespace sb\player\level;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use sb\command\player\FarmingCommand;
use sb\player\CorePlayer;
use sb\player\PlayerManager;
use sb\Skyblock;

class LevelHandler {
	private static array $levels = [];

	public function __construct() {
		self::init(new FarmingLevel("Farming",10, 1, 100, 1.2));
	}

	public static function sendFarmingLevels(CorePlayer $player) {
		$chest = InvMenu::create(InvMenu::TYPE_HOPPER)
			->setName("§r§bFarming §aLevels");

		$chest->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult {
			if($transaction->getOut()->getTypeId() === VanillaBlocks::CHEST()->asItem()->getTypeId()){
				return $transaction->discard()->then(function(CorePlayer $player) : void{
					//self::get("farming")->sendRewardsMenu($player);
				});
			}
			return $transaction->discard();
		});
		$chest->getInventory()->setItem(0, self::get("farming")->getInfoItem($player->getCoreUser()));
		$chest->getInventory()->setItem(1, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLUE())->asItem()->setCustomName("|"));
		$chest->getInventory()->setItem(2, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLUE())->asItem()->setCustomName("|"));
		$chest->getInventory()->setItem(3, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLUE())->asItem()->setCustomName("|"));
		$chest->getInventory()->setItem(4, VanillaBlocks::CHEST()->asItem()->setCustomName("§r§l§bFarming §dLootboxes")->setLore(["§r§7Right-click to claim farming lootboxes"]));

		$chest->send($player);
	}

	private static function init(Level $level) : void {
		self::$levels[strtolower($level->getName())] = $level;
	}

	public static function getAll() : array {
		return self::$levels;
	}

	public static function get(string $level) : Level {
		return self::$levels[strtolower($level)];
	}
}