<?php

declare(strict_types = 1);

namespace sb\item\enchantment;

use pocketmine\form\Form;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\player\Player;
use sb\form\EasyFormButton;
use sb\form\FormHandler;
use sb\inventory\CERarityInventory;
use sb\item\utils\ArmorUtils;
use sb\item\utils\RarityType;
use sb\item\utils\ToolUtils;

class CustomEnchantment extends Enchantment {
	private string $description;

	public function __construct(string $name, int $rarity, string $description, int $maxLevel, int $primaryFlag, int $secondaryFlag = ItemFlags::NONE) {
		$this->description = $description;

		parent::__construct($name, $rarity, $primaryFlag, $secondaryFlag, $maxLevel);
	}

	public function getDescription(): string {
		return $this->description;
	}

	public function getLoreLine(int $level): string {
		$color = RarityType::fromId($this->getRarity())->getColor();

		return $color . $this->getName() . " " . CustomEnchantments::getRomanNumeral($level);
	}

	public function getForm() : Form {
		if($this->hasPrimaryItemType(ItemFlags::ARMOR)) {
			$applicable = "Armor";
		} else if($this->hasPrimaryItemType(ItemFlags::TOOL)) {
			$applicable = "Tools";
		} else {
			$cached = [];

			foreach (array_merge(ToolUtils::TOOL_TO_ITEMFLAG, ArmorUtils::ARMOR_SLOT_TO_ITEMFLAG) as $item => $flag) {
				if ($this->hasPrimaryItemType($flag)) {
					if (isset(ArmorUtils::ARMOR_SLOT_TO_ITEMFLAG[$item])) {
						$cached[] = strtoupper(ArmorUtils::armorSlotToType($item));
					} else {
						$cached[] = basename($item);
					}
				}
			}
			$applicable = implode(", ", $cached);
		}

		return FormHandler::easyForm(
			RarityType::fromId($this->getRarity())->getColor() . $this->getName() . " Enchantment",
			"§bDescription: §7" . $this->getDescription() . "\n§aApplicable to: §7" . $applicable . "\n§bRarity: " . RarityType::fromId($this->getRarity())->getCustomName() . "\n§bMax Level: §4" . $this->getMaxLevel(),
			[
				new EasyFormButton(
					"Back",
					null,
					true,
					function(Player $submitter) : void {
						new CERarityInventory($submitter, RarityType::fromId($this->getRarity()));
					}
				)
			]
		);
	}
}