<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\item\PotionType;
use pocketmine\item\VanillaItems;
use pocketmine\world\sound\PopSound;
use pocketmine\world\sound\XpLevelUpSound;
use sb\item\listeners\ItemInteractListener;
use sb\item\listeners\ItemUseListener;
use sb\lang\CustomKnownTranslationFactory;
use sb\player\CorePlayer;
use sb\Skyblock;

class PotionPouch extends CustomItem implements ItemUseListener {
	public function getName() : string {
		return "Potion Pouch";
	}

	public function getId() : string {
		return CustomItemIds::POTIONPOUCH;
	}

	public function getItem(): Item {
		$pouch = VanillaItems::SPLASH_POTION()->setType(PotionType::STRONG_HEALING());
		$pouch->setCustomName("§r§l§bPotion Package §r§7(Right-Click)");
		$pouch->setLore([
			"§r§7A compressed care package that gives you",
			"§r§7a full inventory of §csplash potions",
			"",
			"§r§bType: §cInsta Health II",
			"§r§l§cNOTE: §r§7You require §c36 §7available slots"
		]);
		$pouch->getNamedTag()->setString("potionpouch", "true");
		self::addNameTag($pouch);
		return $pouch;
	}

	public function onInteract(Item $item, CorePlayer $player, PlayerInteractEvent $event): void{
		$event->cancel();
	}

	public function onUse(Item $item, CorePlayer $player, PlayerItemUseEvent $event): void{
		$event->cancel();

		$tag = $item->getNamedTag()->getString("potionpouch", "");
		if($tag !== ""){
			$item->pop();
			$player->getInventory()->setItemInHand($item);

			$items = [];
			for($i = 0; $i < 36; $i++){
				$items[] = VanillaItems::SPLASH_POTION()->setType(PotionType::STRONG_HEALING());
			}

			$player->getInventory()->addItem(...$items);
			$player->getWorld()->addSound($player->getLocation(), new PopSound());
		}
	}
}