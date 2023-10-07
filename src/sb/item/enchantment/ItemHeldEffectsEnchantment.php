<?php

namespace sb\item\enchantment;

use pocketmine\entity\effect\Effect;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\player\Player;

class ItemHeldEffectsEnchantment extends CustomEnchantment implements ItemHeldEnchantment {
	/** @var EffectInstance[] */
	private array $givenEffects = [];
	/** @var Effect[][] */
	private array $trackedPlayers = [];

	public function __construct(string $name, int $rarity, string $description, int $maxLevel, int $primaryItemFlags, int $secondaryItemFlags, array $givenEffects) {
		parent::__construct($name, $rarity, $description, $maxLevel, $primaryItemFlags, $secondaryItemFlags);
		$this->givenEffects = $givenEffects;

		foreach($this->givenEffects as $k => $v) {
			$this->givenEffects[$k]->setDuration(2147483647);
		}
	}

	public function onHeld(Player $p, int $level) : void {
		$effMgr = $p->getEffects();
		$k = $p->getName();

		foreach($this->givenEffects as $effect) {
			$added = clone $effect;
			$added->setAmplifier($level);
			$effMgr->add($added);
			$this->trackedPlayers[$k][spl_object_id($added)] = $effect->getType();
		}
	}

	public function onUnHeld(Player $p, int $level): void {
		$effMgr = $p->getEffects();
		$k = $p->getName();

		foreach(($this->trackedPlayers[$k] ?? []) as $objId => $effect) {
			if(spl_object_id($effMgr->get($effect)) === $objId) {
				$effMgr->remove($effect);
				unset($this->trackedPlayers[$k][$objId]);
			}
		}
	}
}