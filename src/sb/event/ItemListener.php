<?php

declare(strict_types = 1);

namespace sb\event;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\inventory\CallbackInventoryListener;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\world\sound\XpLevelUpSound;
use sb\item\CustomItemIds;
use sb\item\enchantment\BlockBreakEnchantment;
use sb\item\enchantment\CustomArmorEnchantment;
use sb\item\enchantment\CustomDeathEnchantment;
use sb\item\enchantment\CustomMeleeWeaponEnchantment;
use sb\item\listeners\ItemBlockPlaceListener;
use sb\item\listeners\ItemDamageListener;
use sb\item\listeners\ItemInteractListener;
use sb\item\listeners\ItemInventoryListener;
use sb\item\listeners\ItemTakeDamageListener;
use sb\item\listeners\ItemUseListener;
use sb\item\utils\ArmorUtils;
use sb\item\utils\BaitType;
use sb\item\CustomItem;
use sb\item\sets\DirectAppliableArmor;
use sb\item\CustomItems;
use sb\item\sets\SetArmor;
use sb\item\utils\ToolUtils;
use sb\player\CorePlayer;

class ItemListener implements Listener {
	/**
	 * @priority LOWEST
	 */
	public function onInteract(PlayerInteractEvent $event): void {
		/* @var $player CorePlayer*/
		$player = $event->getPlayer();
		$item = $event->getItem();

		if(($string = $item->getNamedTag()->getString(CustomItem::TAG_CUSTOM_ITEM, "")) !== "") {
			$customItem = CustomItems::fromString($string);

			if($customItem instanceof ItemInteractListener) $customItem->onInteract($item, $player, $event);
		}
	}

	public function onItemUse(PlayerItemUseEvent $event): void {
		/* @var $player CorePlayer*/
		$player = $event->getPlayer();
		$item = $event->getItem();

		if(($string = $item->getNamedTag()->getString(CustomItem::TAG_CUSTOM_ITEM, "")) !== "") {
			$customItem = CustomItems::fromString($string);

			if($customItem instanceof ItemUseListener) $customItem->onUse($item, $player, $event);
		}
	}

	public function onInventoryTransaction(InventoryTransactionEvent $event) {
		$transaction = $event->getTransaction();
		$actions = array_values($transaction->getActions());

		if (count($actions) === 2) {
			foreach ($actions as $i => $action) {
				$itemClickedWith = $action->getTargetItem();
				$itemClicked = $action->getSourceItem();

				$string = $itemClickedWith->getNamedTag()->getString(CustomItem::TAG_CUSTOM_ITEM, "");
				if (
					$action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction
					&& $itemClickedWith->getTypeId() !== VanillaItems::AIR()->getTypeId()
					&& $itemClicked->getTypeId() !== VanillaItems::AIR()->getTypeId()
					&& $itemClicked->getTypeId() == VanillaItems::FISHING_ROD()->getTypeId()
					&& $itemClickedWith->getCount() === 1
					&& $string !== ""
				) {
					if($string == CustomItemIds::FISHING_BAIT) {
						$type = $itemClickedWith->getNamedTag()->getString("baitType", "");

						if($itemClicked->getNamedTag()->getString("baitType", "") !== "") {
							$transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new AnvilFallSound());
							return;
						}
						$event->cancel();
						$lore = $itemClicked->getLore();
						$n = BaitType::fromString($type)->getCustomName();
						$lore[] = "§7§lBait: {$n} §r";
						$itemClicked->setLore($lore);
						$itemClicked->getNamedTag()->setString("baitType", $type);
						$action->getInventory()->setItem($action->getSlot(), $itemClicked);
						$otherAction->getInventory()->setItem($otherAction->getSlot(), VanillaItems::AIR());
						$transaction->getSource()->getWorld()->addSound($transaction->getSource()->getLocation(), new XpLevelUpSound(100));
						return;
					}
				}
			}
		}
	}

	public function onItemInventoryTransaction(InventoryTransactionEvent $ev): void{
		/* @var $player CorePlayer */
		$player = $ev->getTransaction()->getSource();
		$actions = array_values($ev->getTransaction()->getActions());
		if(count($actions) === 2){
			foreach($actions as $i => $action){
				if($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction) {
					$itemClicked = $action->getSourceItem();
					$itemClickedWith = $action->getTargetItem();

					if (($string = $itemClickedWith->getNamedTag()->getString(CustomItem::TAG_CUSTOM_ITEM, "")) !== "") {
						$customItem = CustomItems::fromString($string);

						if ($customItem instanceof ItemInventoryListener) $customItem->onInventoryListen($player, $itemClicked, $itemClickedWith, $action, $otherAction, $ev);
					}
				}
			}
		}
	}

	public function onPlayerJoin(PlayerJoinEvent $event) : void {
		$player = $event->getPlayer();

		if($player instanceof CorePlayer) {
			$player->getArmorInventory()->getListeners()->add(new CallbackInventoryListener(function(Inventory $inventory, int $slot, Item $oldItem) use ($player) : void {
				if(($armor = $player->wearFullArmorSet()) instanceof SetArmor && (empty($player->armor) || $player->armor !== $armor->getColoredName())) {
					/**
					 * @var $armor SetArmor
					 */
					$player->armor = $armor->getColoredName();
					$player->sendMessage("§l{$player->armor}§r§a has been activated!");

					if($armor instanceof DirectAppliableArmor) {
						$armor->applyFullArmor($player);
					}
				} else if(!$player->wearFullArmorSet() instanceof SetArmor && !empty($player->armor)) {
					$player->sendMessage("§l{$player->armor}§r§c has been deactivated!");
					$player->setMovementSpeed(0.1); //RESET ALL
					$player->armor = null;
				}
			}, function(Inventory $inventory, array $oldContents) : void {
			}));
		}
	}

	public function onDamage(EntityDamageEvent $event): void{
		$entity = $event->getEntity();
		if ($event->isCancelled()) return;
		if(!$entity instanceof CorePlayer) return;

		$itemInHand = $entity->getInventory()->getItemInHand();

		if(($string = $itemInHand->getNamedTag()->getString(CustomItem::TAG_CUSTOM_ITEM, "")) !== "") {
			$i = CustomItems::fromString($string);

			if($i instanceof ItemTakeDamageListener) $i->onTakeDamage($itemInHand, $entity, $event);
			if($i instanceof ItemDamageListener) $i->onDamage($itemInHand, $entity, $event);
		}
	}

	public function onExhaust(PlayerExhaustEvent $event) : void {
		$player = $event->getPlayer();
		$item = $player->wearFullArmorSet();

		if ($item instanceof SetArmor && $item->getName() == str_starts_with($item->getName(), "Supreme")) {
			$event->cancel();
			if ($player->getHungerManager()->getFood() < 20)
				$player->getHungerManager()->setFood(20);
		}
	}

	public function onBlockPlace(BlockPlaceEvent $event) {
		$item = $event->getItem();
		/* @var $player CorePlayer */
		$player = $event->getPlayer();

		if(($string = $item->getNamedTag()->getString(CustomItem::TAG_CUSTOM_ITEM, "")) !== "") {
			$i = CustomItems::fromString($string);

			if($i instanceof ItemBlockPlaceListener) $i->onPlaceBlock($item, $player, $event);
		}
	}

	public function checkCEBreak(BlockBreakEvent $event): void {
		$item = $event->getItem();
		if(!$item->hasEnchantments() || !$item instanceof Tool) return;
		$itemFlag = ToolUtils::getToolItemFlag($item);

		foreach($item->getEnchantments() as $enchantment) {
			$type = $enchantment->getType();
			if(!$type->hasPrimaryItemType($itemFlag) or !$type instanceof BlockBreakEnchantment) continue;
			$type->onBreak($event, $enchantment->getLevel());
		}
	}

	public function checkCEDamage(EntityDamageByEntityEvent $event): void {
		$dmg = $event->getDamager();

		if(!$dmg instanceof CorePlayer) return;

		$item = $dmg->getInventory()->getItemInHand();

		if($item instanceof Tool) {
			$itemFlag = ToolUtils::getToolItemFlag($item);

			foreach($item->getEnchantments() as $enchantment) {
				$type = $enchantment->getType();

				if(!$type->hasPrimaryItemType($itemFlag) or !$type instanceof CustomMeleeWeaponEnchantment) continue;
				$type->onDamage($dmg, $event->getEntity(), $enchantment->getLevel(), $event->getFinalDamage());
			}
		}
		$victim = $event->getEntity();

		if(!$victim instanceof CorePlayer) return;

		foreach($victim->getArmorInventory()->getContents() as $slot => $item2) {
			if(!$item2->hasEnchantments()) continue;

			foreach($item2->getEnchantments() as $ench) {
				$type2 = $ench->getType();

				if(!$type2->hasPrimaryItemType(ArmorUtils::ARMOR_SLOT_TO_ITEMFLAG[$slot]) or !$type2->hasSecondaryItemType(ArmorUtils::ARMOR_SLOT_TO_ITEMFLAG[$slot]) or !$type2 instanceof CustomArmorEnchantment) continue;
				$type2->onDamaged($victim, $dmg, $ench->getLevel());
			}
		}
	}

	public function checkCEDeath(EntityDeathEvent $event): void {
		$lastDamageCause = $event->getEntity()->getLastDamageCause();

		if($lastDamageCause instanceof EntityDamageByEntityEvent) {
			if(($killer = $lastDamageCause->getDamager()) instanceof CorePlayer) {
				foreach($killer->getInventory()->getItemInHand()->getEnchantments() as $enchantment) {
					if(($ench = $enchantment->getType()) instanceof CustomDeathEnchantment) $ench->onDeath($killer, $event->getEntity(), $ench->getLevel(), $event);
				}
			}
		}
	}
}
