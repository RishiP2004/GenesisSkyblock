<?php

declare(strict_types=1);

namespace sb\command\player;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use sb\item\CustomItems;
use sb\item\sets\SetArmor;
use sb\item\sets\SetWeapon;
use sb\player\CorePlayer;
//todo: rewrite
class SetsCommand extends BaseCommand {
	protected function prepare() : void{
		$this->setDescription("Sets command");
		$this->setPermission("pocketmine.command.me");
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	/**
	 * @param CorePlayer $sender
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		self::sendSetsMenu($sender);
	}

	public static function sendSetsMenu(CorePlayer $player) : void {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
		$menu->setName("§l§bSaturn Custom Sets");

		$cupid = VanillaItems::BOW()->setCustomName("§r§l§dCupid")->setLore([
			"§r§7§oA armorset of love",
			"§r§7§oas god sent down one of his toughest angels",
			"§r§7§othis savage lover is here.",
			"§r§7§ofor retribution",
			"",
			"§r§l§dEffects:",
            "§r§d* Deal +30% more damage to all enemies.",
            "§r§d* Take -15% less damage from enemies ",
            "§r§d* 58% less combat tag duration",
			"",
			"§r§l§dAbility:",
            "§r§d* Cupid Bow Teleportation Passive Ability",
			"",
			"§r§7Click me to view this armor set."
		]);
		$dragon = VanillaItems::FIRE_CHARGE()->setCustomName("§r§l§eDragon")->setLore([
			"§r§7§oA armorset of the enderdragon",
			"§r§7§oas time has passed the great enderdragon",
			"§r§7§oas died becoming pieces of ancient material.",
			"§r§7§oforged together to become an set",
			"",
			"§r§l§eEffects:",
            "§r§e* +15% PvP Damage",
            "§r§e* Take -20% less damage from enemies",
			"",
			"§r§l§eAbility:",
			"§r§fPower of the EnderDragon",
			"",
			"§r§7Click me to view this armor set."
		]);
		$fantasy = VanillaBlocks::OAK_LEAVES()->asItem()->setCustomName("§r§l§2Fantasy")->setLore([
			"§r§7§oA armorset of the server fantasycloud",
			"§r§7§ofantasycloud is an amazing java server",
			"§r§7§oplay.fanatasycloud.me",
			"§r§7§oCrit battle with this set",
			"§r§7§olol.... youll lose.",
			"",
			"§r§l§2Effects:",
            "§r§2* Gears IV",
            "§r§2* Deal +25% more damage to all enemies.",
            "§r§2* 10% Critical Strike Chance",
			"",
			"§r§l§2Ability:",
            "§r§2* Fantasy Trap Passive Ability",
			"",
			"§r§7Click me to view this armor set."
		]);
		$koth = VanillaBlocks::REDSTONE_TORCH()->asItem()->setCustomName("§r§f:§c§lK§r§8.§l§eO§r§a.§l§bT§r§5.§l§dH§r§f.")->setLore([
			"§r§7§oAn armorset rewarded to only those",
			"§r§7§owho are known throughout the" ,
			"§r§7§opirate realm as Kings!",
			"",
			"§r§l§fEffects:",
			"§r§l§f* §r§f50% chance to negate damage from mobs.",
			"§r§l§f* §r§fPermanent Speed III effect.",
			"§r§l§f* §r§fReceive 10% less damage from players & mobs.",
			"",
			"§r§l§fAbility:",
			"§r§fWhile wearing this set, you are able",
			"§r§fto capture outposts much faster!",
			"",
			"§r§7Click me to view this armor set"
		]);
		$phantom = VanillaItems::REDSTONE_DUST()->setCustomName("§r§l§cPhantom")->setLore([
			"§r§7§oA armorset of the phantom",
			"§r§7§oas death not yet inbued",
			"§r§7§ohe sent his minon aka phantom.",
			"§r§7§othou his damage is unmatched",
			"§r§7§opound for pound try him.",
			"",
			"§r§l§cEffects:",
            "§r§c* §r§cDeal +35% more damage to all enemies.",
            "§r§c* §r§cTake -10% damage from all enemies.",
			"",
			"§r§l§cAbility:",
			"§r§fPhantom Buffs",
			"",
			"§r§7Click me to view this armor set."
		]);
		$ranger = VanillaItems::ARROW()->setCustomName("§r§l§aRanger")->setLore([
			"§r§7§oA armorset of an marksman",
			"§r§7§otagging from far is deadly",
			"§r§7§oone head shot could end anyone.",
			"§r§7§otho not pound for pound",
			"§r§7§olets go arrow for arrow ;D.",
			"",
			"§r§l§aEffects:",
            "§r§a* §r§aEnemies bows do -25% less damage to you.",
            "§r§a* §r§aRanger bow grants +30% increased bow damage.",
			"",
			"§r§l§aAbility:",
			"§r§fHead Shot Archer",
			"",
			"§r§7Click me to view this armor set."
		]);
		$reaper = VanillaBlocks::MOB_HEAD()->asItem()->setCustomName("§r§l§4Reaper")->setLore([
			"§r§7§oA armorset of death",
			"§r§7§ophantom couldnt handle his task",
			"§r§7§odeath is here n hes coming for all.",
			"§r§7§our soul will be his for the takings",
			"",
			"§r§l§4Effects:",
            "§r§4* Deal +30% more damage to all enemies.",
            "§r§4* Take 15% less damage from enemies",
			"",
			"§r§l§4Ability:",
            "§r§4* Mark of the Reaper Passive Ability",
			"",
			"§r§7Click me to view this armor set."
		]);
		$spooky = VanillaBlocks::LIT_PUMPKIN()->asItem()->setCustomName("§r§l§6Spooky")->setLore([
			"§r§7§oA armorset of the night",
			"§r§7§oas the headless horseman has lost himself",
			"§r§7§oforever wondering whos head is next.",
			"§r§7§oforever fear as hes coming for you",
			"§r§7§oadvantage.",
			"",
			"§r§l§6Effects:",
			"§r§l§f* §r§fDeal +20% more damage to all enemies.",
			"§r§l§f* §r§fTake -20% less damage from enemies",
			"§r§l§f* §r§fHalloweenify passive ability",
			"",
			"§r§l§6Ability:",
			"§r§fHalloweenify passive ability",
			"",
			"§r§7Click me to view this armor set."
		]);
		$supreme = VanillaBlocks::LIT_PUMPKIN()->asItem()->setCustomName("§r§l§4Supreme")->setLore([
			"§r§7§oA armorset of the night",
			"§r§7§oas the headless horseman has lost himself",
			"§r§7§oforever wondering whos head is next.",
			"§r§7§oforever fear as hes coming for you",
			"§r§7§oadvantage.",
			"",
			"§r§l§6Effects:",
			"§r§l§f* §r§fDeal +20% more damage to all enemies.",
			"§r§l§f* §r§fTake -20% less damage from enemies",
			"§r§l§f* §r§fHalloweenify passive ability",
			"",
			"§r§l§6Ability:",
			"§r§fHalloweenify passive ability",
			"",
			"§r§7Click me to view this armor set."
		]);
		$thor = VanillaItems::DIAMOND_AXE()->setCustomName("§r§l§bThor")->setLore([
			"§r§7§oA armorset of the god of lightning",
			"§r§7§obanished from asgard",
			"§r§7§ohis hammer followed him landing on Saturn",
			"§r§7§ohis lightning will strike you to",
			"§r§7§ohell.",
			"",
			"§r§l§bEffects:",
            "§r§b* Take -15% less damage from enemies",
            "§r§b* 25% less combat tag duration",
			"",
			"§r§l§bAbility:",
            "§r§b* Mjolnir Passive Ability",
			"",
			"§r§7Click me to view this armor set."
		]);
		$traveler = VanillaBlocks::OBSIDIAN()->asItem()->setCustomName("§r§l§5Traveler")->setLore([
			"§r§7§oA armorset of the time-traveler",
			"§r§7§oskipping thru time and space",
			"§r§7§oDimension to Dimension.",
			"§r§7§oas worlds collide until his influence",
			"",
			"§r§l§5Effects:",
            "§r§5* §r§5You deal +30% more damage.",
			"",
			"§r§l§5Ability:",
            "§r§5* §r§5Dimensional Shift Passive Ability ",
			"",
			"§r§7Click me to view this armor set."
		]);
		$xmas = VanillaBlocks::SNOW()->asItem()->setCustomName("§l§l§cX§2M§aA§fS")->setLore([
			"§r§7§oA armorset of the krampus",
			"§r§7§oan demon known as krampus has came",
			"§r§7§osecond to known toe to toe with death?.",
			"§r§7§osleep tight he might just BITE",
			"",
			"§r§l§2Effects:",
            "§r§2* Deal +20% more damage to all enemies.",
            "§r§2* Take -15% less damage from enemies",
			"",
			"§r§l§2Ability:",
            "§r§2* Active Snowify Ability",
			"",
			"§r§7Click me to view this armor set."
		]);
		$yeti = VanillaItems::IRON_AXE()->setCustomName("§r§l§bYeti")->setLore([
			"§r§7§oA armorset of the yeti",
			"§r§7§otho not as strong as others",
			"§r§7§oan normal human cannot withstand him.",
			"§r§7§othis axe chop hills... it will chop you",
			"",
			"§r§l§bEffects:",
            "§r§b* §r§bDeal +10% more damage to all enemies,",
            "§r§b* §r§bEnemies deal -10% less damage to you.",
			"",
			"§r§l§bAbility:",
			"§r§fChop Chop",
			"",
			"§r§7Click me to view this armor set."
		]);
		$yijki = VanillaBlocks::WITHER_ROSE()->asItem()->setCustomName("§r§l§fYijki")->setLore([
			"§r§7§oA armorset of the mother of yijki",
			"§r§7§oshes pissed... who can calm her down",
			"§r§7§odeath has a mother n shes come to return him.",
			"§r§7§oto hell", 
			"",
			"§r§l§fEffects:",
            "§r§f* §r§fEnemies deal -30% less damage to you.",
			"",
			"§r§l§fAbility:",
            "§r§f* §r§fRevenge Of Yijki Passive Ability",
			"",
			"§r§7Click me to view this armor set."
		]);
		$menu->getInventory()->setContents([$koth, $spooky, $cupid, $dragon, $supreme, $phantom, $ranger, $reaper, $thor, $traveler, $xmas, $yeti, $yijki]);
		$menu->setListener(function(InvMenuTransaction $transaction){
			self::sendSetsViewMenu($transaction->getPlayer(), $transaction->getItemClicked()->getCustomName());
			return $transaction->discard();
		});
		$menu->send($player);
	}

	public static function sendSetsViewMenu(CorePlayer $player, $set) : void {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
		$menu->setName($set . " Set");

		foreach(CustomItems::getAll() as $item) {
			if($item instanceof SetArmor or $item instanceof SetWeapon) {
				$set = TextFormat::clean($set);
				$replaced = str_replace([
					'Helmet',
					'Chestplate',
					'Leggings',
					'Boots',
					'Sword',
					'Axe',
					'Bow'
				], '', $item->getName());
				//for koth
				if(strtolower($replaced) == strtolower(preg_replace("/[^A-Z]+/", "", $set))) $menu->getInventory()->addItem($item->getItem());

				if(strtolower($replaced) == strtolower($set)) $menu->getInventory()->addItem($item->getItem());
			}
		}
		$menu->setListener(function(InvMenuTransaction $transaction) {
			if(!$transaction->getPlayer()->hasPermission("sets.command.use")) return $transaction->discard();
			else return $transaction->continue();
		});
		$menu->send($player);
	}
}