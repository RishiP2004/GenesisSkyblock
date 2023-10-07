<?php

namespace sb\world\sound;

use pocketmine\entity\Entity;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

final class SoundPlayer {
	public static function play(Entity $player, string $sound, $volume = 1, $pitch = 1, int $radius = 5): void {
		foreach ($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy($radius, $radius, $radius)) as $p) {
			if ($p instanceof Player) {
				if ($p->isOnline()) {
					$spk = new PlaySoundPacket();
					$spk->soundName = $sound;
					$spk->x = $p->getLocation()->getX();
					$spk->y = $p->getLocation()->getY();
					$spk->z = $p->getLocation()->getZ();
					$spk->volume = $volume;
					$spk->pitch = $pitch;
					$p->getNetworkSession()->sendDataPacket($spk);
				}
			}
		}
	}
}