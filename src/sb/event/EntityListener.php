<?php

namespace sb\event;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use sb\entity\EntityManager;
use sb\player\CorePlayer;

class EntityListener implements Listener {
	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void {
		$player = $event->getOrigin()->getPlayer();
		$pk = $event->getPacket();

		if($player instanceof CorePlayer) {
			if($pk->pid() == InventoryTransactionPacket::NETWORK_ID) {
				if($pk->trData->getTypeId() == InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
					if($pk->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_ATTACK) {
						$entity = $pk->trData;

						foreach(EntityManager::getInstance()->getAllNPCs() as $NPC) {
							if($entity->getActorRuntimeId() === $NPC->getEntityId()) $NPC->onInteract($player);
						}
					}
				}
			}
		}
	}

	public function onPlayerMove(PlayerMoveEvent $event) : void {
		$player = $event->getPlayer();

		foreach(EntityManager::getInstance()->getAllNPCs() as $NPC) $NPC->rotateTo($player);
	}
}