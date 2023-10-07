<?php

declare(strict_types=1);

namespace sb\player\p2p;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\item\VanillaItems;
use sb\database\Database;
use sb\player\CorePlayer;
use sb\scheduler\player\RollCoinflipTask;
use sb\Skyblock;

final class CoinflipHandler {
	public static array $coinflips = [];

	private static array $cfCache = [];

	const colors = [
		"e" => "yellow",
		"a" => "green",
		"d" => "purple",
		"b" => "light blue",
		"6" => "orange",
		"8" => "black",
		"7"=> "gray",
		"c" => "red"
	];

	public function __construct() {
		Database::get()->asyncGeneric("coinflips.init");
		Database::get()->executeSelect("coinflips.getAll", [], function (array $rows): void {
			foreach ($rows as $row) {
				self::$coinflips[$row["player"]] = new Coinflip($row["player"], $row["color"], $row["amount"], $row["used"]);
			}
		});
	}
	/**
	 * @return Coinflip[]
	 */
	public static function getAll() : array {
		return self::$coinflips;
	}

	public static function get(CorePlayer $player) : ?Coinflip {
		return self::$coinflips[$player->getName()] ?? null;
	}

	public static function add(CorePlayer $player, string $color, int $amount) : void {
		Database::get()->asyncInsert("coinflips.update", ["player" => strtolower($player->getName()), "color" => $color, "amount" => $amount, "used" => false]);

		self::$coinflips[$player->getName()] = new Coinflip($player->getName(), $color, $amount);
	}

	public static function remove(CorePlayer $player) : void {
		Database::get()->asyncGeneric("coinflips.remove", ["player" => strtolower($player->getName())]);
		self::$coinflips[$player->getName()]->setUsed(); //incase some weird timing
		unset(self::$coinflips[$player->getName()]);
	}

	public static function sendMenu(CorePlayer $player) : void {
		$id = 0;

		foreach(CoinflipHandler::getAll() as $k => $v) {
			if(strtolower($v->getPlayer()) !== strtolower($player->getName())) {
				$id++;
				$cfs[$id] = VanillaItems::GOLD_INGOT()->setCustomName("§f§l" . $v->getPlayer() . "\n§r§c$" . number_format($v->getAmount()));
				self::$cfCache[$id] = $v;
			}
		}
		$cfs[20] = VanillaBlocks::WOOL()->asItem()->setCustomName("§6§lRefresh\n§r§7Click to refresh matches.");

		$menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
		$menu->setName("Coinflips");
		$menu->getInventory()->setContents($cfs);
		$menu->send($player);

		$menu->setListener(function(InvMenuTransaction $transaction) use ($player, $menu, $cfs) {
			if($transaction->getItemClicked()->getTypeId() == VanillaBlocks::WOOL()->asItem()->getTypeId()) {
				$menu->getInventory()->setContents($cfs);
			} else {
				$cf = self::$cfCache[$transaction->getAction()->getSlot()];

				if($cf->isUsed()) {
					$player->sendMessage(Skyblock::ERROR_PREFIX . "This coin flip already ended");
				}
				self::sendViewMenu($player, $cf);
			}
			return $transaction->discard();
		});
	}

	public static function sendViewMenu(CorePlayer $player, Coinflip $coinflip) : void {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
		$menu->setName("Coinflip - " . $player->getName() . " ($" . $coinflip->getAmount() . ")");
		$menu->getInventory()->setItem(12, VanillaBlocks::WOOL()->setColor(DyeColor::GREEN())->asItem()->setCustomName("§2§lAccept wager\n§7Click to accept the wager"));
		$menu->getInventory()->setItem(14, VanillaBlocks::WOOL()->setColor(DyeColor::RED())->asItem()->setCustomName("§6§lBack\n§7Back to coin flip matches"));
		$menu->send($player);

		$menu->setListener(function(InvMenuTransaction $transaction) use ($coinflip, $player, $menu) {
			if($transaction->getAction()->getSlot() == 12) {
				self::sendSelectColorMenu($player, $coinflip, null);
			} if($transaction->getAction()->getSlot() == 14) {
				self::sendMenu($player);
			}

			return $transaction->discard();
		});
	}

	private static array $validColors = [
		"e" => "yellow",
		"a" => "green",
		"d" => "purple",
		"b" => "light_blue",
		"6" => "orange",
		"8" => "black",
		"7" => "gray",
		"c" => "red"
	];

	public static function sendSelectColorMenu(CorePlayer $player, ?Coinflip $cf = null, ?int $amount = null) {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);

		$content = [];

		$colors = [
			"yellow" => DyeColor::YELLOW(),
			"green" => DyeColor::GREEN(),
			"purple" => DyeColor::PURPLE(),
			"light_blue" => DyeColor::LIGHT_BLUE(),
			"orange" => DyeColor::ORANGE(),
			"black" => DyeColor::BLACK(),
			"gray" => DyeColor::GRAY(),
			"red" => DyeColor::RED()
		];

		$cache = [];

		$slot = 10;
		foreach(self::$validColors as $id => $color){
			if($cf?->getColor() === $color) continue;

			$content[$slot] = VanillaBlocks::WOOL()
				->setColor($colors[$color])
				->asItem()
				->setCustomName("§l§" . $id . strtoupper($color) . "\n§r§7Click to pick " . ucwords($color));

			$cache[$slot] = $color;

			$slot++;
		}

		$menu->getInventory()->setContents($content);

		$menu->setListener(function(InvMenuTransaction $transaction) use ($cache, $amount, $colors, $cf) : InvMenuTransactionResult{
			$player = $transaction->getPlayer();
			$item = $transaction->getItemClicked();

			if($cf === null) {
				$player->sendMessage(Skyblock::PREFIX . "Coinflip created successfully.");

				self::add($player, $cache[$transaction->getAction()->getSlot()], $amount);
				$player->getCoreUser()->reduceMoney($amount);
				$player->removeCurrentWindow();
			} else {
				$menu = InvMenu::create(InvMenuTypeIds::TYPE_HOPPER);
				$menu->setName("Coinflip");

				$menu->setListener(InvMenu::readonly());

				$item->setCustomName("§r|");

				$head = VanillaBlocks::MOB_HEAD()
					->setMobHeadType(MobHeadType::PLAYER())
					->asItem()
					->setCustomName("§r§e" . $player->getName());

				$menu->getInventory()->setContents([
					3 => $item,
					4 => $head
				]);

				$menu->setInventoryCloseListener(function(CorePlayer $player, Inventory $inventory) use ($menu){
					$item = $inventory->getItem(0);
					if($player->isOnline() && ($tag = $item->getNamedTag()->getTag("isEnded")) !== null){
						if($tag->getValue() !== 1) $menu->send($player);
					}
				});
				Skyblock::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(
					new RollCoinflipTask($menu, $player,
						[
							VanillaBlocks::WOOL()
								->setColor($colors[$cf->getColor()])
								->asItem(),
							$item
						],
						$cf), 10, 20);
			}

			return $transaction->discard();
		});

		$menu->send($player, "Pick a coinflip color");
	}
}