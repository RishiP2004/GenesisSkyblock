<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\ItemUseResult;
use pocketmine\item\Releasable;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use sb\item\utils\BaitType;
use sb\entity\FishingHook;
use sb\event\player\PlayerFishEvent;
use sb\player\CorePlayer;
use sb\server\ServerData;

final class FishingRod extends Durable implements Releasable {
	private static array $cache = [];

	/**
	 * @param CorePlayer $player
	 */
	public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems): ItemUseResult {
		if(isset(self::$cache[$player->getName()])){
			/** @var FishingHook $e */
			$e = self::$cache[$player->getName()];
			if(!$e->isClosed() && !$e->isFlaggedForDespawn()){
				if($e->isActive()){
					$this->onCatch($player);
				}

				self::$cache[$player->getName()]->flagForDespawn();

				self::$cache[$player->getName()] = null;
				return parent::onClickAir($player, $directionVector, $returnedItems);
			}
		}

		$loc = $player->getLocation();
		$loc->y += 1;
		$e = new FishingHook($loc, $player);

		$e->setMaxFishTime((mt_rand((int) self::getMinFishTimeInSeconds(), (int) self::getMaxFishTimeInSeconds())));

		$e->handleHookCasting($player->getDirectionVector()->multiply(2), 2, 2);
		$e->setOwningEntity($player);
		$e->spawnToAll();
		self::$cache[$player->getName()] = $e;

		return parent::onClickAir($player, $directionVector, $returnedItems);
	}

	public function onCatch(Player $player): void {
		$reward = $this->getRandomReward();
		$event = new PlayerFishEvent($player, $this, $reward, mt_rand(1, 5));
		$event->call();

		$player->getInventory()->canAddItem($reward) ? $player->getInventory()->addItem($reward) : $player->getWorld()->dropItem($player->getPosition()->asVector3(), $reward);

		$player->sendMessage(ServerData::PREFIX . "Found§r§c " . $reward->getCount() . "x " . $reward->getName());
	}

	public function getRandomReward(): Item {
		$items = [
			VanillaItems::APPLE(),
			VanillaItems::GOLD_INGOT()
		];

		//would double up the chance from these random items.
		if($this->getNamedTag()->getString("baitType", "") != "") {
			$type = BaitType::fromString($this->getNamedTag()->getString("baitType"));

			foreach($type->getRewardsToFocus() as $focus) {
				$items[] = $focus;
			}
		}
		return $items[array_rand($items)];
	}

	public function getMaxStackSize() : int{
		return 1;
	}

	public static function getMinFishTimeInSeconds(): float {
		return 100;
	}

	public static function getMaxFishTimeInSeconds(): float {
		return 200;
	}

	public function getMaxDurability() : int {
		return 384;
	}

	public function canStartUsingItem(Player $player) : bool {
		return true;
	}
}