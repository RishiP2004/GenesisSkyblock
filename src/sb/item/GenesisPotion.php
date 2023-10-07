<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\world\sound\ThrowSound;
use sb\entity\CustomPotion;
use sb\item\listeners\ItemUseListener;
use sb\player\CorePlayer;
use sb\world\crates\CratesHandler;

class GenesisPotion extends CustomItem implements ItemUseListener {
	private string $type;

	public function getName() : string {
		return "Genesis Potion";
	}

	public function getItem(string $type) : Item {
		$this->type = $type;
		$item = VanillaItems::SPLASH_POTION();

		$name = CratesHandler::get($type)->getColouredName();

		$item->setCustomName("§r{$name} §r§aKey");
		$item->getNamedTag()->setString("type", CratesHandler::get($type)->getName());
		self::addNameTag($item);
		return $item;
	}

	public function onUse(Item $item, CorePlayer $player, PlayerItemUseEvent $event) : void {
		$event->cancel();
		$directionVector = $event->getDirectionVector();
		$location = $player->getLocation();
		$projectile = new CustomPotion(Location::fromObject($player->getEyePos(), $player->getWorld(), $location->yaw, $location->pitch), $player, $this->potionType);
		$projectile->setMotion($directionVector->multiply(.7));

		$projectileEv = new ProjectileLaunchEvent($projectile);
		$projectileEv->call();

		if($projectileEv->isCancelled()) {
			$projectile->flagForDespawn();
			return;
		}
		$projectile->spawnToAll();
		$location->getWorld()->addSound($location, new ThrowSound());
		$item->pop();
	}

	public function getId() : string {
		return CustomItemIds::GENESIS_POTION;
	}
}