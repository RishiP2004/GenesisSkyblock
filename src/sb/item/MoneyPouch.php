<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\world\sound\XpLevelUpSound;
use sb\item\listeners\ItemUseListener;
use sb\lang\CustomKnownTranslationFactory;
use sb\player\CorePlayer;
use sb\Skyblock;

class MoneyPouch extends CustomItem implements ItemUseListener {
	public function getName() : string {
		return "Money Pouch";
	}

	public function getId() : string {
		return CustomItemIds::MONEYPOUCH;
	}

	public function getItem(int $tier): Item {
		$pouch = VanillaBlocks::CHEST()->asItem();
		$pouch->setCustomName("§r§l§dGenesis Pouch §r§7(Right-Click) §f(#0130)");
		switch ($tier){
			case 1:
				$pouch->setLore([
					"§r§7A mysterious pouch that can grant",
					"§r§7fortunes or disappointment",
					"",
					"§r§l§dTier: §r§7I - (one)",
					"§r§7rewards range from 50k - 100k"
				]);

				break;
			case 2:
				$pouch->setLore([
					"§r§7A mysterious pouch that can grant",
					"§r§7fortunes or disappointment",
					"",
					"§r§l§dTier: §r§7II - (two)",
					"§r§7rewards range from 100k - 250k"
				]);
				break;
			case 3:
				$pouch->setLore([
					"§r§7A mysterious pouch that can grant",
					"§r§7fortunes or disappointment",
					"",
					"§r§l§dTier: §r§7III - (three)",
					"§r§7rewards range from 250k - 500k"
				]);
				break;
			case 4:
				$pouch->setLore([
					"§r§7A mysterious pouch that can grant",
					"§r§7fortunes or disappointment",
					"",
					"§r§l§dTier: §r§7IV - (four)",
					"§r§7rewards range from 500k - 1m"
				]);
				break;
		}
		$min = match ($tier){
			1 => 50000,
			2 => 100000,
			3 => 250000,
			4 => 500000,
			default => 0
		};

		$max = match ($tier){
			1 => 100000,
			2 => 250000,
			3 => 500000,
			4 => 1000000,
			default => 0
		};
		$pouch->getNamedTag()->setInt("min", $min);
		$pouch->getNamedTag()->setInt("max", $max);
		self::addNameTag($pouch);
		return $pouch;
	}

	public function onUse(Item $item, CorePlayer $player, PlayerItemUseEvent $event) : void {
		$event->cancel();

		$min = $item->getNamedTag()->getInt("min", 0);
		$max = $item->getNamedTag()->getInt("max", 0);

		if($min > 0 && $max > 0){
			$item->pop();
			$player->getInventory()->setItemInHand($item);

			$amount = mt_rand($min, $max);
			$player->getCoreUser()->addMoney($amount);

			$player->sendMessage(CustomKnownTranslationFactory::money_pouch_redeem(number_format($amount, 1)));
			$player->getWorld()->addSound($player->getLocation(), new XpLevelUpSound(500));
		}
	}
}