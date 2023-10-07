<?php

namespace sb\item\enchantment;

use pocketmine\entity\effect\Effect;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\player\Player;
//todo: check for ones that need all pieces
class ArmorEffectEquipmentEnchantment extends ArmorEquipmentEnchantment {
	/** @var EffectInstance[] */
	private array $givenEffects;
	/** @var Effect[][] */
	private array $trackedPlayers = [];

	public function __construct(string $name, int $rarity, string $description, int $primaryItemFlags, int $secondaryItemFlags, int $maxLevel, array $givenEffects, ?int $forcedLevel = null) {
		parent::__construct($name, $rarity, $description, $primaryItemFlags, $secondaryItemFlags, $maxLevel);
		$this->givenEffects = $givenEffects;

		foreach($this->givenEffects as $k => $v) {
			$this->givenEffects[$k]->setDuration(2147483647);
		}
	}

	public function onEquip(Player $p, int $level): void {
		$effMgr = $p->getEffects();
		$k = $p->getName();
		foreach($this->givenEffects as $effect) {
			$added = clone $effect;
			$added->setAmplifier($level - 1);
			$effMgr->add($added);
			$this->trackedPlayers[$k][spl_object_id($added)] = $effect->getType();
		}
	}

	public function onRemove(Player $p, int $level): void {
		$effMgr = $p->getEffects();
		$k = $p->getName();

		foreach(($this->trackedPlayers[$k] ?? []) as $objId => $effect) {
			if(!$effMgr->has($effect)) {
				unset($this->trackedPlayers[$k][$objId]);
				continue;
			}
			if(spl_object_id($effMgr->get($effect)) === $objId) {
				$effMgr->remove($effect);
				unset($this->trackedPlayers[$k][$objId]);
			}
		}
	}
}