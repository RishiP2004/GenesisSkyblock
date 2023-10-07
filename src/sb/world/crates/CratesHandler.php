<?php

namespace sb\world\crates;

use pocketmine\block\VanillaBlocks;
use pocketmine\math\Facing;
use pocketmine\Server;
use pocketmine\world\Position;

class CratesHandler implements Crates {
	/**
	 * @var Crate[]
	 */
	private static array $crates = [];

	public function __construct() {
		foreach(self::CRATES as $name => $data) {
			$posEx = explode(", ", $data["pos"]);
			$pos = new Position($posEx[0], $posEx[1], $posEx[2], Server::getInstance()->getWorldManager()->getWorldByName($posEx[3]));

			$crate = new Crate(
				$name,
				$pos,
			);
			self::init($crate);

			$pos->getWorld()->orderChunkPopulation($pos->getX() >> 4, $pos->getZ() >> 4, null)->onCompletion(function() use($crate, $pos) : void {
				$world = $pos->getWorld();
				$tile = $world->getTile($pos);

				if(!$tile instanceof \sb\block\tile\Crate) {
					$block = VanillaBlocks::CHEST();
					$block->setFacing(1); //todo
					$world->setBlock($pos, VanillaBlocks::CHEST());
					$tile = $world->getTile($pos);
					$tile->close();

					$newTile = new \sb\block\tile\Crate($world, $pos, true);
					$newTile->setType($crate);
					$newTile->init();
					$world->addTile($newTile);
				} else if(!$tile->isInitialized()) {
					$tile->setType($crate);
					$tile->init();

					if($tile->getType()->getName() !== $crate->getName()) {
						$tile = $world->getTile($pos);
						$tile->close();
						$newTile = new \sb\block\tile\Crate($world, $pos, true);

						$newTile->setType($crate);
						$world->addTile($newTile);
					}
				}
			}, function() : void {});
		}
		/**$manager->registerCommands("player",
		new keycommand(Skyblock::getInstance(), "givekey"),
		);*/
	}

	public static function init(Crate $crate) : void {
		self::$crates[strtolower($crate->getName())] = $crate;
	}

	/**
	 * @return Crate[]
	 */
	public static function getAll() : array {
		return self::$crates;
	}

	public static function get(string $crate) : ?Crate {
		return self::$crates[strtolower($crate)] ?? null;
	}
}