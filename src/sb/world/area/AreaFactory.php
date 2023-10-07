<?php

namespace sb\world\area;

use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\Position;
use sb\utils\ResourceInitializer;

final class AreaFactory {
	use SingletonTrait;

	/**
	 * @var Area[]
	 */
	private array $areas = [];

	public function __construct() {
		foreach(yaml_parse_file(ResourceInitializer::getPath("config"). "areas.yml") as $data) {
			$pos1Ex = explode(", ", $data["pos1"]);
			$pos1 = new Position((float) $pos1Ex[0], (float)$pos1Ex[1], (float)$pos1Ex[2], Server::getInstance()->getWorldManager()->getWorldByName($pos1Ex[3]));
			$pos2Ex = explode(", ", $data["pos2"]);
			$pos2 = new Position((float) $pos2Ex[0], (float)$pos2Ex[1], (float)$pos2Ex[2], Server::getInstance()->getWorldManager()->getWorldByName($pos2Ex[3]));

			$area = new Area(
				$data["name"],
				$pos1,
				$pos2,
				$data["pvp"],
				$data["edit"],
				$data["title"],
				$data["msg"]
			);
			$this->register($data["name"], $area);
		}
	}
	public function register(string $id, Area $area) : void {
		$this->areas[$id] = $area;
	}

	public function get(string $id) : Area {
		return $this->areas[$id];
	}
	/**
	 * @return Area[]
	 */
	public function getAll() : array{
		return $this->areas;
	}

	public function getInPosition(Position $position) : ?array {
		$areas = $this->getAll();
		$areasInPosition = [];

		foreach($areas as $area) {
			if($area->isPositionInside($position) === true) {
				$areasInPosition[] = $area;
			}
		}
		if(empty($areasInPosition)) {
			return null;
		}
		return $areasInPosition;
	}
}