<?php

namespace sb\item\sets;

use sb\player\CorePlayer;

interface DirectAppliableArmor {
	public function applyFullArmor(CorePlayer $player) : void;
}