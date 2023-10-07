<?php

declare(strict_types = 1);

namespace sb\utils;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;

final class EntityUtils {
	public static function getSkinDataFromPNG(string $path): string {
		$image = imagecreatefrompng($path);
		$data = "";
		for($y = 0, $height = imagesy($image); $y < $height; $y++) {
			for($x = 0, $width = imagesx($image); $x < $width; $x++) {
				$color = imagecolorat($image, $x, $y);
				$data .= pack("c", ($color >> 16) & 0xFF)
					. pack("c", ($color >> 8) & 0xFF)
					. pack("c", $color & 0xFF)
					. pack("c", 255 - (($color & 0x7F000000) >> 23));
			}
		}
		return $data;
	}

	public static function createSkin(string $skinData) : Skin {
		return new Skin("Standard_Custom", $skinData, "", "geometry.humanoid.custom");
	}

	public static function fakeDeath(Location $location, string $entityTypeId): void {
		$fakeEntityPacket = AddActorPacket::create(
			$id = Entity::nextRuntimeId(),
			$id,
			$entityTypeId,
			$location->asVector3(),
			new Vector3(0, 0, 0),
			$location->pitch,
			$location->yaw,
			$location->yaw,
			$location->yaw,
			[],
			[],
			new PropertySyncData([], []),
			[]
		);
		$deathPacket = ActorEventPacket::create($id, ActorEvent::DEATH_ANIMATION, 0);

		$location->getWorld()->broadcastPacketToViewers($location, $fakeEntityPacket);
		$location->getWorld()->broadcastPacketToViewers($location, $deathPacket);
	}
}