<?php

namespace sb\player\slotbot;

use sb\item\CustomItems;
//todo: clean this up
final class SlotBotHandler {
	//todo: rewrite into config
	public static function getRewards() : array {
		return [
			CustomItems::MONEYPOUCH()->getItem(2),
			CustomItems::MONEYPOUCH()->getItem(1),
			CustomItems::MONEYPOUCH()->getItem(2),
			CustomItems::SLOTBOT_TICKET()->getItem()->setCount(2),
			//rank kit redeemable
			//perm perks
			CustomItems::MONEYPOUCH()->getItem(2),
			//key bundle
			CustomItems::YIJKI_HELMET()->getItem(),
			CustomItems::SELLWAND()->getItem(10),
		];
	}
}
