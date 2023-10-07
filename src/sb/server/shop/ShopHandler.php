<?php

namespace sb\server\shop;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\block\BlockLegacyMetadata;
use pocketmine\data\bedrock\item\upgrade\LegacyItemIdToStringIdMap;
use pocketmine\inventory\Inventory;
use pocketmine\item\Dye;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use sb\item\CustomItems;
use sb\player\CorePlayer;
use sb\server\shop\entry\ShopEntry;
use sb\Skyblock;
use Shuchkin\SimpleXLSX;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ShopHandler {
	private static array $cats = [];
	private static array $sellPrices = [];

	public function __construct() {
		Skyblock::getInstance()->saveResource("shop.xlsx");
		$xl = SimpleXLSX::parse(Skyblock::getInstance()->getDataFolder() . "shop.xlsx");
		$lines = 0;
		$cats = [];
		$cat = "";

		foreach($xl->rows() as $row){
			if($lines === 292) break;

			if($row[0] === "Category :") {
				$cat = $row[1];
			} else if($row[0] !== "Items") {
				try {
					$it = explode(":", $row[1]);
					$dyeColorNames = [
						1 => "Orange Stained Glass Pane",
						2 => "Magenta Stained Glass Pane",
						3 => "Light Blue Stained Glass Pane",
						4 => "Yellow Stained Glass Pane",
						5 => "Lime Stained Glass Pane",
						6 => "Pink Stained Glass Pane",
						7 => "Gray Stained Glass Pane",
						8 => "Light Gray Stained Glass Pane",
						9 => "Cyan Stained Glass Pane",
						10 => "Purple Stained Glass Pane",
						11 => "Blue Stained Glass Pane",
						12 => "Brown Stained Glass Pane",
						13 => "Green Stained Glass Pane",
						14 => "Red Stained Glass Pane",
						15 => "Black Stained Glass Pane",
					];

					$i = LegacyStringToItemParser::getInstance()->parse($it[0] . ":" . $it[1]) ?? StringToItemParser::getInstance()->parse($it[0] . ":" . $it[1]);
					if($i instanceof Dye) {
						if (isset($dyeColorNames[(int)$it[1]])) {
							$i = VanillaBlocks::STAINED_GLASS_PANE();
							$i->setColor(match ((int)$it[1]){
								1 => DyeColor::ORANGE(),
								2 => DyeColor::MAGENTA(),
								3 => DyeColor::LIGHT_BLUE(),
								4 => DyeColor::YELLOW(),
								5 => DyeColor::LIME(),
								6 => DyeColor::PINK(),
								7 => DyeColor::GRAY(),
								8 => DyeColor::LIGHT_GRAY(),
								9 => DyeColor::CYAN(),
								10 => DyeColor::PURPLE(),
								11 => DyeColor::BLUE(),
								12 => DyeColor::BROWN(),
								13 => DyeColor::GREEN(),
								14 => DyeColor::RED(),
								15 => DyeColor::BLACK(),
								default => throw new \Exception('Unexpected match value'),
							});
							$i = $i->asItem()->setCustomName($dyeColorNames[(int)$it[1]]);
						}
					}


					$sell = $row[3];

					if(trim($row[3]) == 'Not Sellable') $sell = null;

					//for further sell uses.
					if(!is_null($sell)) {
						self::$sellPrices[$i->hasCustomName() ? $i->getCustomName() : $i->getName()] = (int)$sell;
					}

					$buy = $row[2];
					if(trim($row[2]) == 'Not Buyable') $buy = null;

					$cats[$cat][] = new ShopEntry($i->hasCustomName() ? $i->getCustomName() : $i->getName(), $i, $buy, $sell);
				} catch(\Exception $e) {
					continue;
				}
			}
			$lines++;
		}
		foreach($cats as $cat => $entries) {
			self::$cats[$cat] = new Category($cat, $this->getCategoryItemFor($cat), $entries);
		}
		/**
		 * static $nameToMeta = [
		10 => "Chicken",
		11 => "Cow",
		12 => "Pig",
		17 => "Squid",
		20 => "Iron Golem",
		32 => "Zombie",
		36 => "Zombie Pigman",
		34 => "Skeleton",
		37 => "Slime",
		16 => "Mooshroom",
		43 => "Blaze",
		];
		 */
		self::$cats["Mob Spawners"] = new Category("Mob Spawners", $this->getCategoryItemFor("Mob Spawners"), [
			new ShopEntry("Chicken Spawner", StringToItemParser::getInstance()->parse("Chicken_spawner")->setCustomName("Chicken Spawner"), 250000, 30000),
			new ShopEntry("Cow Spawner", StringToItemParser::getInstance()->parse("Cow_spawner")->setCustomName("Cow Spawner"), 350000, 40000),
			new ShopEntry("Pig Spawner", StringToItemParser::getInstance()->parse("Pig_spawner")->setCustomName("Pig Spawner"), 400000, 45000),
			new ShopEntry("Squid Spawner", StringToItemParser::getInstance()->parse("Squid_spawner")->setCustomName("Squid Spawner"), 500000, 50000),
			new ShopEntry("Iron Golem Spawner", StringToItemParser::getInstance()->parse("Iron Golem_spawner")->setCustomName("Iron Golem Spawner"), 1000000, 100000),
			new ShopEntry("Zombie Spawner", StringToItemParser::getInstance()->parse("Zombie_spawner")->setCustomName("Zombie Spawner"), 500000, 50000),
			new ShopEntry("Zombie Pigman Spawner", StringToItemParser::getInstance()->parse("Zombie Pigman_spawner")->setCustomName("Zombie Pigman Spawner"), 500000, 50000),
			new ShopEntry("Skeleton Spawner", StringToItemParser::getInstance()->parse("Skeleton_spawner")->setCustomName("Skeleton Spawner"), 500000, 50000),
			new ShopEntry("Slime Spawner", StringToItemParser::getInstance()->parse("Slime_spawner")->setCustomName("Slime Spawner"), 500000, 50000),
			new ShopEntry("Mooshroom Spawner", StringToItemParser::getInstance()->parse("Mooshroom_spawner")->setCustomName("Mooshroom Spawner"), 500000, 50000),
			new ShopEntry("Blaze Spawner", StringToItemParser::getInstance()->parse("Blaze_spawner")->setCustomName("Blaze Spawner"), 500000, 50000),


		]);
	}
	/**
	 * @return Category[]
	 */
	public static function getCategories() : array {
		return self::$cats;
	}

	public static function getCategory(string $name): ?Category {
		return self::$cats[$name] ?? null;
	}

	public static function getCategoryItemFor(string $name) : Item {
		return match($name) {
			"Minerals" => VanillaBlocks::EMERALD_ORE()->asItem()->setCustomName("§r§c§lMinerals")->setLore(["§r§7Click to view this category."]),
			"Farm" => VanillaItems::WHEAT()->setCustomName("§r§c§lFarm")->setLore(["§r§7Click to view this category."]),
			"Decoration" => VanillaItems::BRICK()->setCustomName("§r§c§lDecoration")->setLore(["§r§7Click to view this category."]),
			"Coloured Blocks" => VanillaBlocks::POPPY()->asItem()->setCustomName("§r§c§lColoured Blocks")->setLore(["§r§7Click to view this category."]),
			"Misc" => VanillaItems::GOLDEN_APPLE()->setCustomName("§r§c§lMisc")->setLore(["§r§7Click to view this category."]),
			"Mob Drops" => VanillaItems::RAW_BEEF()->setCustomName("§r§c§lMob Drops")->setLore(["§r§7Click to view this category."]),
			"Brewing Supplies" => VanillaBlocks::BREWING_STAND()->asItem()->setCustomName("§r§c§lBrewing Supplies")->setLore(["§r§7Click to view this category."]),
			"Potions" => VanillaItems::SPLASH_POTION()->setCustomName("§r§c§lPotions")->setLore(["§r§7Click to view this category."]),
			"Base Building" => VanillaBlocks::END_STONE()->asItem()->setCustomName("§r§c§lBase Building")->setLore(["§r§7Click to view this category."]),
			"Raiding" => VanillaBlocks::TNT()->asItem()->setCustomName("§r§c§lRaiding")->setLore(["§r§7Click to view this category."]),
			"Fish" => VanillaItems::RAW_FISH()->setCustomName("§r§c§lFish")->setLore(["§r§7Click to view this category."]),
			"Mob Spawners" => VanillaBlocks::MONSTER_SPAWNER()->asItem()->setCustomName("§r§c§lMob Spawners")->setLore(["§r§7Click to view this category."]),
		};
	}

	public static function sendMenu(CorePlayer $player) : void {
		$chest = InvMenu::create(InvMenu::TYPE_CHEST)
			->setName("Shop Categories");
		$chest->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult {
			$item = $transaction->getItemClicked();

			return $transaction->discard()->then(function(Player $player) use($item) : void{
				if(is_null($item)) return;
				self::getCategory(TextFormat::clean($item->getCustomName()))->send($player);
			});
		});
		$content = self::getCategories();

		foreach($content as $con) {
			$chest->getInventory()->addItem($con->getDisplayItem());
		}
		$chest->send($player);
	}

	public static function sellInventory(CorePlayer $player, Inventory $inventory) : int {
		$gain = 0;

		foreach($inventory->getContents() as $slot => $content) {
			if(in_array($content->getName(), self::$sellPrices)) {
				$gain += self::$sellPrices[$content->getName()] * $content->getCount();
				$inventory->clear($slot);
			}
		}
		$player->getCoreUser()->addMoney($gain);
		return $gain;
	}
}