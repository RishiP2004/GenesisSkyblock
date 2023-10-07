<?php

declare(strict_types=1);

namespace sb\item\enchantment;
//Used for when an enchantment needs another
interface ChildEnchantmentRequiredEnchantment {
	public function getChildEnchantmentId() : int;
}