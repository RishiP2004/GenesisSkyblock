<?php

declare(strict_types=1);

namespace sb\item;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use sb\item\enchantment\CustomEnchantmentIdentifiers;

abstract class CustomItem {
	public const TAG_CUSTOM_ITEM = "CustomItem";
	public const TAG_ID = "uniqueId";

	public abstract function getName(): string;

	public abstract function getId(): string;

	protected function addNameTag(Item $item): void {
		$item->getNamedTag()->setString(self::TAG_CUSTOM_ITEM, $this->getId());
	}

	public static function applyDisplayEnchant(Item $item): void {
		$item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(CustomEnchantmentIdentifiers::FAKE_ENCH_ID)));
	}
}