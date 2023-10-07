<?php

namespace sb\player\kit;

use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\PopSound;
use sb\permission\CorePermissionManager;
use sb\player\kit\category\GroupedCategories;
use sb\player\kit\category\KitCategory;
use sb\player\kit\types\free\EarthKit;
use sb\player\kit\types\free\JupiterKit;
use sb\player\kit\types\free\MarsKit;
use sb\player\kit\types\free\MemberKit;
use sb\player\kit\types\free\MercuryKit;
use sb\player\kit\types\free\SaturnKit;
use sb\Skyblock;
use sb\utils\ResourceInitializer;

class KitHandler{
	use SingletonTrait;

	/** @var Kit[] $kits */
	private array $kits = [];

	/** @var KitCategory[] $kitCategories */
	private array $kitCategories = [];

	public function __construct() {
		$this->setInstance($this);
		$this->registerKits();

		$kitCategoriesConfig = new Config(ResourceInitializer::getPath("config") . "kitcategories.yml", Config::YAML);
		foreach ($kitCategoriesConfig->get("categories") as $index => $categoryData) {
			if (isset($this->kitCategories[$index])) continue;
			try {
				$tier = $categoryData["tier"] ?? 0;
				$iconData = $categoryData["icon"] ?? [];

				$item = $this->createCategoryItem($iconData, $index);
				$kits = $this->getKitsByTier($tier);

				$this->kitCategories[$index] = new KitCategory($index, $tier, $item, $kits);
			} catch (\Exception $e) {
				Skyblock::getInstance()->getLogger()->error("Failed to load kit category $index: " . $e->getMessage());
			}
		}
	}

	private function registerKits(): void{
		$this->registerKit(new MemberKit());
		$this->registerKit(new MercuryKit());
		$this->registerKit(new EarthKit());
		$this->registerKit(new MarsKit());
		$this->registerKit(new JupiterKit());
		$this->registerKit(new SaturnKit());
	}

	private function createCategoryItem(array $iconData, string $index): Item {
		$material = $iconData["material"] ?? null;
		$name = TextFormat::colorize($iconData["name"] ?? $index);
		$lore = array_map(fn(string $line) => TextFormat::colorize($line), $iconData["lore"] ?? []);

		if (!$material) {
			throw new \InvalidArgumentException("Missing 'material' for kit category '$index'");
		}

		try {
			$item = StringToItemParser::getInstance()->parse($material);
			$item->setCustomName($name);
			$item->setLore($lore);
		} catch (\Throwable $e) {
			throw new \RuntimeException("Failed to create item for kit category '$index': " . $e->getMessage(), 0, $e);
		}
		return $item;
	}


	private function getKitsByTier(int $tier): array {
		$array = [];

		foreach ($this->kits as $kit){
			if(isset($array[$kit->getName()])) continue;

			if($kit->tier === $tier){
				$array[$kit->getName()] = $kit;
			}
		}
		return $array;
	}

	private function registerKit(Kit $kit): void {
		if (isset($this->kits[strtolower($kit->getName())])) return;

		$this->kits[strtolower($kit->getName())] = $kit;
	}

	public function isKit(string $name): bool {
		return isset($this->kits[strtolower($name)]);
	}

	public function getKit(string $name): ?Kit {
		return $this->kits[strtolower($name)] ?? null;
	}

	public function getKitCategory(string $identifier): ?KitCategory {
		return $this->kitCategories[$identifier] ?: null;
	}

	public function getKitCategoryByTier(int $tier): ?KitCategory {
		foreach ($this->kitCategories as $kitCategory) {
			if ($kitCategory->getTier() === $tier) return $kitCategory;
		}
		return null;
	}

	public function getKitCategories(): array {
		return $this->kitCategories;
	}

	public function getGroupedCategories(): GroupedCategories {
		return new GroupedCategories($this->kitCategories);
	}

	public function getKits(): array {
		return $this->kits;
	}
}
