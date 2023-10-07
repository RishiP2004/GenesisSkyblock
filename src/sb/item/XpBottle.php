<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\event\Event;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\utils\TextFormat as C;
use pocketmine\item\VanillaItems;
use pocketmine\world\sound\ItemBreakSound;
use sb\item\listeners\ItemUseListener;
use sb\lang\CustomKnownTranslationFactory;
use sb\player\CorePlayer;
use sb\Skyblock;

class XpBottle extends CustomItem implements ItemUseListener {
	public function getName(): string {
		return "Xp Bottle";
	}

	public function getItem(int $amount, string $signer = "Console") : Item {
		$item = VanillaItems::EXPERIENCE_BOTTLE();
		$item->setCustomName("§r§a§lExperience Bottle §r§7(Throw)");
		$item->setLore([
			"§r§dValue: §r§f" . number_format($amount),
			"§r§dEnchanter: §r§f" . $signer,
		]);
		$item->getNamedTag()->setString("xpBottle", "true");
		$item->getNamedTag()->setString("uniqueID", $id = uniqid("" . mt_rand(1, 100)));
		$item->getNamedTag()->setInt($this->getName(), $amount);

		self::addNameTag($item);
		return $item;
	}

	public function onInteract(Item $item, CorePlayer $player, PlayerInteractEvent $event): void{
		$event->cancel();
	}


	public function onUse(Item $item, CorePlayer $player, PlayerItemUseEvent $event) : void {
		$event->cancel();
		$amount = $item->getNamedTag()->getInt($this->getName(), 0);
		$tag = $item->getNamedTag()->getString("xpBottle", "");

		if($tag !== "") {
			$item->pop();
			$player->getInventory()->setItemInHand($item);
			$player->getXpManager()->addXp($amount);

			$location = $player->getLocation();
			$x = $location->x;
			$y = $location->y;
			$z = $location->z;

			$player->getNetworkSession()->sendDataPacket(PlaySoundPacket::create("random.glass", $x, $y, $z, 1, 0.44));
			$player->sendMessage(CustomKnownTranslationFactory::xp_bottle_success(number_format($amount)));
		}
	}

	public function getId() : string {
		return CustomItemIds::XPBOTTLE;
	}
}