<?php

namespace sb\entity\npc;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\Server;
use sb\player\CorePlayer;
use sb\Skyblock;
use sb\utils\EntityUtils;

class KitNPC extends NPC {
	public function __construct() {
		parent::__construct(
			"kit",
			new Location(270, 111, 230, Server::getInstance()->getWorldManager()->getDefaultWorld(), 10, 10),
			"§r§eKit Giver",
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
		$player->chat("/kit");
	}
}