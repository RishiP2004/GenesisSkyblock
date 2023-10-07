<?php

namespace sb\entity\npc;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\Server;
use pocketmine\world\sound\ClickSound;
use sb\lang\CustomKnownTranslationFactory;
use sb\player\CorePlayer;
use sb\Skyblock;
use sb\utils\EntityUtils;

class ShopNPC extends NPC {
	public function __construct() {
		parent::__construct(
			"shop",
			new Location(267, 111, 234, Server::getInstance()->getWorldManager()->getDefaultWorld(), 10, 10),
			"§r§a§lShop\n \n§r§fBuy from and §asell §r§fto the server\nwith official §r§aprices. §rBe wise and\n§rbecome one of the §r§arichest §rplayers!\n \n §r§a§lRight Click §r§fto open this NPC!\n \n",
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
		$player->chat("/shop");
		$player->getWorld()->addSound($player->getLocation(), new ClickSound());
	}
}