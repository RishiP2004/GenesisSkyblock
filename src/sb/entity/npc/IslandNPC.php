<?php

namespace sb\entity\npc;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\Server;
use sb\islands\Island;
use sb\islands\traits\IslandCallTrait;
use sb\player\CorePlayer;
use sb\Skyblock;
use sb\utils\EntityUtils;

class IslandNPC extends NPC {
	use IslandCallTrait;

	public function __construct() {
		parent::__construct(
			"island",
			new Location(263, 108, 249, Server::getInstance()->getWorldManager()->getDefaultWorld(), 10, 10),
			"§r§6§lIsland Helper\n§r§f§l 0 §r§c§l❤",
			2
		);
	}
	//todo: save skins
	public function getSkin() : Skin {
		$path = Skyblock::getInstance()->getDataFolder() . "skins" . DIRECTORY_SEPARATOR . "test.png";
		return EntityUtils::createSkin(EntityUtils::getSkinDataFromPNG($path));
	}

	public function getSize() : EntitySizeInfo {
		return new EntitySizeInfo(3, 2, 2);
	}

	public function onInteract(CorePlayer $player) : void {
		$hasIsland = $player->getCoreUser()->hasIsland();

		if($hasIsland){
			$this->getIsland($player->getCoreUser()->getIsland(), function (Island $island) use ($player) {
				$island->teleport($player);
			});
		}else{
			$player->chat("/is");
		}
	}
}