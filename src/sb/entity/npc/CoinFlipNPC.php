<?php

namespace sb\entity\npc;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\Server;
use sb\player\CorePlayer;
use sb\Skyblock;
use sb\utils\EntityUtils;

class CoinFlipNPC extends NPC {
	public function __construct() {
		parent::__construct(
			"coinflip",
			new Location(272, 111, 232, Server::getInstance()->getWorldManager()->getDefaultWorld(), 10, 10),
			"§r§e§lCoinFlip\n \n§r§fFlip a coin and test your luck\n against other players to gain\n§ror lose in-game money!\n \n §r§e§lRight Click §r§fto open this NPC!\n \n",
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
		$player->chat("/coinflip"); //PLEASE SOMEONE ELSE I CANNOT
	}
}