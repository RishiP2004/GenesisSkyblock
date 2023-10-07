<?php
namespace sb\inventory;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use sb\lang\CustomKnownTranslationFactory;
use sb\player\CorePlayer;
use sb\scheduler\player\TeleportTimerTask;
use sb\server\warps\Warp;
use sb\Skyblock;

class WarpsInventory extends BasicInventory{
	private array $slotToWarpMap = [];
	public function __construct(public readonly array $warps = []){
		parent::__construct(InvMenuTypeIds::TYPE_HOPPER);
	}


	public function createInventory(): void{
		$this->setName("Genesis Warps");
		$slot = 0;

		foreach ($this->warps as $warpName => $warp){
			$this->slotToWarpMap[$slot] = $warpName;
			/** @var Warp $warp */
			$this->inventory->setItem($slot++, $warp->getTexture());
		}
	}

	public function createListener(): void{
		$this->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) : void{
			/** @var CorePlayer $player */
			$player = $transaction->getPlayer();
			$slot = $transaction->getAction()->getSlot();

			if($player->isTeleporting()){
				$player->sendMessage(CustomKnownTranslationFactory::player_already_teleporting());
				return;
			}

			/** @var Warp $warp */
			$warp = $this->warps[$this->slotToWarpMap[$slot]];
			$position = $warp->getPosition();

			Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new TeleportTimerTask($player, $position, 7), 20);
			$player->removeCurrentWindow();
		}));
	}
}