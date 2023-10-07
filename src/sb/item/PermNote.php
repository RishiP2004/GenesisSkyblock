<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\permission\Permission;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\AmethystBlockChimeSound;
use sb\item\listeners\ItemUseListener;
use sb\lang\CustomKnownTranslationFactory;
use sb\player\CorePlayer;
use sb\Skyblock;

class PermNote extends CustomItem implements ItemUseListener {
	const FLY_PERM = "/fly";
	const FIX_PERM = "/fix";

	public function getName(): string {
		return "Perm Note";
	}

	public function getId() : string {
		return CustomItemIds::PERM_NOTE;
	}

	public function getItem(string $perm) : Item {
		$item = VanillaItems::PAPER()->setCustomName(TextFormat::colorize("&r&d&lPERK '&r&f$perm&r&d&l' &r&7(Right-Click) &r&f(#0339)"));
		$item->setLore([
			TextFormat::colorize("&r&7Right-click this gem to unlock the"),
			TextFormat::colorize("&r&f$perm&r&7 command on this dimension."),
		]);
		$item->getNamedTag()->setString("perm", $perm);
		self::addNameTag($item);
		return $item;
	}

	public function onUse(Item $item, CorePlayer $player, PlayerItemUseEvent $event) : void {
		static $permTranslated = [
			self::FLY_PERM => "fly.command",
			self::FIX_PERM => "fix.command"
		];
		$event->cancel();
		$perm = $item->getNamedTag()->getString("perm");

		if($perm !== ""){
			if($player->getCoreUser()->hasPermission($permTranslated[$perm])) {
				$player->sendMessage(Skyblock::ERROR_PREFIX . "You already have this permission.");
				return;
			}
			$item->pop();
			$player->getInventory()->setItemInHand($item);

			$player->getCoreUser()->addPermission(new Permission($permTranslated[$perm]));
			$player->sendMessage(CustomKnownTranslationFactory::perk_grant_permission($perm));
			$player->getWorld()->addSound($player->getLocation(), new AmethystBlockChimeSound());
		}
	}
}