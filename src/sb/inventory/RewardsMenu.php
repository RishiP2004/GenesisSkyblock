<?php

namespace sb\inventory;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\VanillaBlocks;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\Server;
use sb\player\CorePlayer;
use sb\server\ServerData;
use sb\server\ServerManager;
use sb\Skyblock;
use sb\utils\MathUtils;

final class RewardsMenu {
	public static function send(CorePlayer $player) : void {
		$timeD = $player->getCoreUser()->getRewardTimeFor(ServerData::DAILY_REWARD);
		$timeM = $player->getCoreUser()->getRewardTimeFor(ServerData::MONTHLY_REWARD);

		$time1 = ($timeD === null) ? 0 : ($timeD + 3600 * 24) - time();
		$time2 = ($timeM === null) ? 0 : ($timeM + 3600 * 24 * 30) - time();

		$time1Trans = ($timeD === null) ? "NOW" : MathUtils::secondsToTime($time1)["h"] . " hours, " . MathUtils::secondsToTime($time1)["s"] . " sec";
		$time2Trans = ($timeM === null) ? "NOW" : MathUtils::secondsToTime($time2)["d"] . " days, " . MathUtils::secondsToTime($time2)["h"] . " hours, " . MathUtils::secondsToTime($time2)["s"] . " sec";

		$menu = InvMenu::create(InvMenu::TYPE_HOPPER);
		$inv = $menu->getInventory();
		$menu->setName("§r§4Rewards");
		$inv->setItem(1, VanillaBlocks::CHEST()->asItem()->setCustomName("§r§a§lDAILY REWARD")->setLore(["", "§r§7Your free daily reward!", "§r§7Time until next claim: " . $time1Trans, "", "§r§aClick to claim it!"]));
		$inv->setItem(3, VanillaBlocks::CHEST()->asItem()->setCustomName("§r§b§lMonthly REWARD")->setLore(["", "§r§7Your free monthly reward!", "§r§7Time until next claim: " . $time2Trans, "", "§r§bClick to claim it!"]));

		$menu->send($player);
		$menu->setListener(function(InvMenuTransaction $transaction) use($time1, $time2) : InvMenuTransactionResult {
			/**
			 * @var CorePlayer @player
			 */
			$player = $transaction->getPlayer();
			$true = false;

			if($transaction->getAction()->getSlot() == 1) {
				if ($time1 > 0) {
					$player->sendMessage(Skyblock::ERROR_PREFIX . "Cannot claim daily reward yet");
					return $transaction->discard();
				}
				$cmds = ServerManager::getInstance()->getReward(ServerData::DAILY_REWARD)->getCmds();
				$items = ServerManager::getInstance()->getReward(ServerData::DAILY_REWARD)->getItems();

				$true = true;
				$player->getCoreUser()->setRewardTime(ServerData::DAILY_REWARD, time());
				$player->sendMessage(Skyblock::PREFIX . "Claimed daily reward!");
			} else if($transaction->getAction()->getSlot() == 3) {
				if ($time2 > 0) {
					$player->sendMessage(Skyblock::ERROR_PREFIX . "Cannot claim monthly reward yet");
					return $transaction->discard();
				}
				$cmds = ServerManager::getInstance()->getReward(ServerData::MONTHLY_REWARD)->getCmds();
				$items = ServerManager::getInstance()->getReward(ServerData::MONTHLY_REWARD)->getItems();

				$true = true;
				$player->getCoreUser()->setRewardTime(ServerData::MONTHLY_REWARD, time());
				$player->sendMessage(Skyblock::PREFIX . "Claimed monthly reward!");
			}
			if($true) {
				foreach($cmds as $cmd) {
					Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), str_replace('{PLAYER}', $player->getName(), $cmd));
				}
				foreach($items as $item) {
					if($item instanceof Item) {
						$player->getInventory()->canAddItem($item) ? $player->getInventory()->addItem($item) : $player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
					}
				}
			}
			return $transaction->discard()->then(function(CorePlayer $player) : void {
				$player->removeCurrentWindow();
			});
		});
	}
}