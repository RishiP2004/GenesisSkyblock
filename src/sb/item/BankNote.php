<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat as C;
use pocketmine\item\VanillaItems;
use pocketmine\world\sound\XpCollectSound;
use pocketmine\world\sound\XpLevelUpSound;
use sb\item\listeners\ItemUseListener;
use sb\lang\CustomKnownTranslationFactory;
use sb\player\CorePlayer;
use sb\Skyblock;

class BankNote extends CustomItem implements ItemUseListener {
	public function getName(): string {
		return "Bank Note";
	}

	public function getItem(int $amount, string $signer = "console") : Item {
		$item = VanillaItems::PAPER();
		$format = number_format($amount);

		$item->setCustomName("§r§3§lBank Note §r§7(Right-Click)");
		$item->setLore([
			"§r§7Value: §r§3" . "$" .$format,
			"§r§7Signer: §r§3" . $signer,
		]);
		$item->getNamedTag()->setString("uniqueId", uniqid("" . mt_rand(1, 100)));
		$item->getNamedTag()->setString("signer", $signer);
		$item->getNamedTag()->setInt($this->getName(), $amount);

		self::addNameTag($item);
		return $item;
	}

	public function getId() : string {
		return CustomItemIds::BANKNOTE;
	}

	public function onUse(Item $item, CorePlayer $player, PlayerItemUseEvent $event) : void {
		$event->cancel();
		$amount = $item->getNamedTag()->getInt($this->getName(), 0);
		$id = $item->getNamedTag()->getString(self::TAG_ID, "");
		if($amount > 0 && $id !== ""){
			$item->pop();
			$player->getInventory()->setItemInHand($item);

			$player->getCoreUser()->addMoney($amount);
			$player->getWorld()->addSound($player->getLocation(), new XpCollectSound());
			$player->sendMessage(CustomKnownTranslationFactory::bank_note_redeem(number_format($amount,1)));
		}
	}
}