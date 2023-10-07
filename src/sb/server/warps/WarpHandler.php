<?php

namespace sb\server\warps;

use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\Position;
use sb\Skyblock;
use pocketmine\utils\TextFormat as TF;

class WarpHandler{
	use SingletonTrait;
	/** @var Warp[] */
	private array $warps = [];

	public function __construct(private readonly Skyblock $core) {
		self::setInstance($this);
		$warpsConfig = new Config($this->core->getDataFolder() . "warps.yml", Config::YAML);

		foreach ($warpsConfig->get("warps", []) as $index => $warpData){
			if(isset($this->warps[$index])) continue;

			try{
				$iconData = $warpData["icon"] ?? [];
				$positionData = $warpData["position"] ?? [];

				$position = new Position($positionData[0], $positionData[1], $positionData[2], $this->core->getServer()->getWorldManager()->getWorldByName($warpData["level"]));
				$item = $this->createWarpItem($warpData, $index);

				$this->warps[$index] = new Warp($index, $item, $position);
			}catch (\Exception $e){
				Skyblock::getInstance()->getLogger()->error("Failed to load warp $index: " . $e->getMessage());
				continue;
			}
		}
	}

	private function createWarpItem(array $iconData, string $index): Item{
		$material = $iconData["material"] ?? null;
		$name = TF::colorize($iconData["name"] ?? $index);
		$lore = array_map(fn(string $line) => TF::colorize($line), $iconData["lore"] ?? []);

		if(!$material){
			throw new \InvalidArgumentException("Missing 'material' for warp '$index'");
		}

		try{
			$item = StringToItemParser::getInstance()->parse($material);
			$item->setCustomName($name);
			$item->setLore($lore);
		}catch(\InvalidArgumentException $e){
			throw new \InvalidArgumentException("Invalid 'material' for warp '$index': " . $e->getMessage(), $e->getCode(), $e);
		}

		return $item;
	}

	public function getWarps(): array{
		return $this->warps;
	}
}