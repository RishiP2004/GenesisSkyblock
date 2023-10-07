<?php

namespace sb\item;

use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\world\format\io\GlobalItemDataHandlers;

final class ItemOverrider {
	public static function override(string $name, Item $item): void {
		StringToItemParser::getInstance()->override($name, fn() => $item);
	}

	public static function setDeserializer(string $typeName, Item $item, \Closure $deserialize = null) {
		(function(string $id, \Closure $deserializer): void {
			$this->deserializers[$id] = $deserializer;
		})->call(
			GlobalItemDataHandlers::getDeserializer(),
			$typeName,
			$deserialize ?? fn() => clone $item
		);
	}

	public static function setSerializer(string $typeName, Item $item, \Closure $serialize = null) {
		(function(Item $item, \Closure $serializer): void {
			$this->itemSerializers[$item->getTypeId()] = $serializer;
		})->call(
			GlobalItemDataHandlers::getSerializer(),
			$item,
			$serialize ?? fn() => new SavedItemData($typeName)
		);
	}

	public static function setBlockSerializer(string $typeName, Item $item, \Closure $serialize = null) {
		(function(Item $item, \Closure $serializer): void {
			$this->blockItemSerializers[$item->getTypeId()] = $serializer;
		})->call(
			GlobalItemDataHandlers::getSerializer(),
			$item,
			$serialize ?? fn() => new SavedItemData($typeName)
		);
	}

	public static function resetCreativeInventory() : void {
		CreativeInventory::reset();
		CreativeInventory::getInstance();
	}
}
