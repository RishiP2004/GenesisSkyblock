<?php

namespace sb\player\level;

use pocketmine\block\Block;
use pocketmine\block\Crops;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\world\sound\ExplodeSound;
use pocketmine\world\sound\XpCollectSound;
use sb\block\tile\Crop;
use sb\islands\Island;
use sb\islands\utils\IslandStats;
use sb\islands\traits\IslandCallTrait;
use sb\player\CorePlayer;
use sb\player\CoreUser;
use sb\Skyblock;

class FarmingLevel extends Level {
	use IslandCallTrait;

	public function onLevelup(CorePlayer $player, int $oldLvl, int $newLvl): void {
		parent::onLevelup($player, $oldLvl, $newLvl);

		if(($level = $player->getWorld()) !== null && $player->isOnline()) {
			$level->addSound($player->getLocation(), new ExplodeSound(), [$player]);
			$player->sendMessage("\n\n§b§lFarming level up §c$oldLvl §b-> §a$newLvl\n\n"); //TODO TEST THIS
		}
	}

	public function getXPGain(Block $block): int {
		return match ($block->getTypeId()) {
			VanillaBlocks::WHEAT()->getTypeId() => mt_rand(2, 4),
			VanillaBlocks::POTATOES()->getTypeId() => mt_rand(13, 17),
			VanillaBlocks::BEETROOTS()->getTypeId() => mt_rand(34, 39),
			VanillaBlocks::MELON()->getTypeId() => mt_rand(62, 71),
			VanillaBlocks::PUMPKIN()->getTypeId() => mt_rand(127, 143),
			VanillaBlocks::SUGARCANE()->getTypeId() => mt_rand(574, 624),
			default => 0,
		};
	}

	public function getNeededLevelForFarm(Block $block): ?int {
		return match ($block->getTypeId()) {
			VanillaBlocks::CARROTS()->getTypeId() => 0,
			VanillaBlocks::NETHER_WART()->getTypeId() => 0,
			VanillaBlocks::WHEAT()->getTypeId() => 0,
			VanillaBlocks::POTATOES()->getTypeId() => 10,
			VanillaBlocks::BEETROOTS()->getTypeId() => 20,
			VanillaBlocks::MELON()->getTypeId() => 40,
			VanillaBlocks::PUMPKIN()->getTypeId() => 60,
			VanillaBlocks::SUGARCANE()->getTypeId() => 80,
			default => null,
		};
	}

	public function handle(CorePlayer $player, Event $event) : void {
		if($event instanceof BlockBreakEvent) {
			$block = $event->getBlock();

			if($event->isCancelled()) return;

			if(in_array($block->getTypeId(),
				[
					VanillaBlocks::CARROTS()->getTypeId(),
					VanillaBlocks::NETHER_WART()->getTypeId(),
					VanillaBlocks::WHEAT()->getTypeId(),
					VanillaBlocks::POTATOES()->getTypeId(),
					VanillaBlocks::BEETROOTS()->getTypeId(),
					VanillaBlocks::MELON_STEM()->getTypeId(),
					VanillaBlocks::PUMPKIN_STEM()->getTypeId(),
					VanillaBlocks::SUGARCANE()->getTypeId()
				])) {
				if($this->canFarm($player, $block)){
					if($block instanceof Crops) {
						if($block->getAge() >= 7){
							$this->onMine($player, $block);
						}
					} else {
						$tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
						if (!$tile instanceof Crop) {
							$this->onMine($player, $block);
						}
					}
				} else {
					$player->sendPopup(Skyblock::ERROR_PREFIX . "Cannot farm this yet. Must levelup!");
					$event->cancel();
				}
			}
		}
	}

	public function canFarm(CorePlayer $player, Block $block) : bool {
		if(!is_null($lvl = $this->getNeededLevelForFarm($block))) {
			return $player->getCoreUser()->getLevel($this) >= $lvl;
		}
		return false;
	}

	public function onMine(CorePlayer $player, Block $block): void {
		$xp = $this->getXPGain($block);

		if($xp === 0) return;

		$this->addXp($player, $xp);

		if($player->getCoreUser()->hasIsland() ) {
			$this->getIsland($player->getCoreUser()->getIsland(), function(?Island $island) use ($player) {
				if(is_null($island)) {
					return false;
				}
				$island->addStat(IslandStats::CROPS_HARVESTED, IslandStats::XP[IslandStats::CROPS_HARVESTED]);
				return true;
			});
		}
	}

	public function onXpGained(CorePlayer $player, int $amount) : void {
		$player->broadcastSound(new XpCollectSound(), [$player]);
		$player->sendActionBarMessage("§l§a+$amount FARMING EXP ");
	}

	public function getInfoItem(CoreUser $user): Item {
		$item = VanillaItems::PAPER();

		$wheat = "§a";
		$potato = $user->getLevel($this) >= 10 ? "§a" : "§c";
		$beet = $user->getLevel($this) >= 20 ? "§a" : "§c";
		$melon = $user->getLevel($this) >= 40 ? "§a" : "§c";
		$pump = $user->getLevel($this) >= 60 ? "§a" : "§c";
		$sugar = $user->getLevel($this) >= 80 ? "§a" : "§c";
		$nether = "§c";

		$item->setCustomName('§r§l§aFarming Levels');
		$item->setLore([
			"§r§7Farming Level! You require to be a certain level to",
			"§r§7be able to farm a different crop. For each different",
			"§r§7crop, the experience gain is different. As you level up",
			"§r§7the required experience gained is higher and higher.",
			"§r",
			"§r§l§aCROPS",
			"§r{$wheat}§l * §r{$wheat}Wheat (2 to 4 Farming EXP)",
			"§r§7(FARMING LVL REQUIRED: Any)",
			"§r{$potato}§l * §r{$potato}Potato (13 to 17 Farming EXP)",
			"§r§7(FARMING LVL REQUIRED: 10)",
			"§r{$beet}§l * §r{$beet}Beetroot (34 to 39 Farming EXP)",
			"§r§7(FARMING LVL REQUIRED: 20)",
			"§r{$melon}§l * §r{$melon}Melon (62 to 71 Farming EXP)",
			"§r§7(FARMING LVL REQUIRED: 40)",
			"§r{$pump}§l * §r{$pump}Pumpkin (127 to 143 Farming EXP)",
			"§r§7(FARMING LVL REQUIRED: 60)",
			"§r{$sugar}§l * §r{$sugar}Sugarcane (574 to 624 Farming EXP)",
			"§r§7(FARMING LVL REQUIRED: 80)",
			"§r{$nether}§l * §r{$nether}Netherwart (1,204 to 1,348 Farming EXP)",
			"§r§7(FARMING LVL REQUIRED: 100)",
			"§r",
			"§r§l§c1. §r§cYou don't get any Farming EXP if you don't",
			"§r§cmatch the required level.",
			"§r§l§c2. §r§cCertain buffs can increase your",
			"§r§cfarming experience gain depending on the buff.",
		]);
		return $item;
	}
	//todo;
	/**
	public function sendRewardsMenu() : void {
		$menu = InvM::create(InvMenu::TYPE_CHEST);
		$menu->setName("§r§bFarming §dLootboxes");

		/**
		 * @var int  $level
		 * @var Item $box */
		/**
		foreach($this->getBoxes() as $level => $box){
		$lore = $box->getLore();
		$lore[] = "";

		if(0 >= $level){
		if(((bool) $this->session->getRedis()->get("player.{$this->session->getUsername()}.farming.reward.$level")) === true){
		$lore[] = "§cAlready Claimed";
		} else $lore[] = "§aRight-Click To Claim";
		} else $lore[] = "§cNot Claimable";

		$box->getNamedTag()->setInt("farmingLevelTag", $level);
		$box->setLore($lore);

		$menu->getInventory()->addItem($box);
		}

		return $menu;
		}
		 *
		 *
		public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();

		$lvl = $transaction->getOut()->getNamedTag()->getInt("farmingLevelTag", -1);

		if($lvl > -1){
		if($this->level < $lvl){
		$player->sendMessage(Main::PREFIX . "You need to be farming level §c$lvl §7to claim this lootbox.");
		return;
		}
		if(((bool) $this->session->getRedis()->get("player.{$this->session->getUsername()}.farming.reward.$lvl")) === true){
		$player->sendMessage(Main::PREFIX . "You have already claimed the farming level §c$lvl §7lootbox");
		return;
		}
		$this->session->getRedis()->set("player.{$this->session->getUsername()}.farming.reward.$lvl", true);
		Utils::addItem($player, $this->getBoxes()[$lvl]);
		$player->sendMessage(Main::PREFIX . "Claimed the §c$lvl §7 farming lootbox");
		}
		}
		 */
	public function getRewards() : array {
		return [];
	}
}