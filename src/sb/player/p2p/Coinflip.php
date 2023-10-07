<?php

declare(strict_types=1);

namespace sb\player\p2p;

class Coinflip {
	public function __construct(
		private readonly string $player,
		private readonly string $color,
		private readonly int    $amount,
		private bool $used = false
	){ }

	public function getPlayer() : string {
		return $this->player;
	}

	public function getAmount() : int {
		return $this->amount;
	}

	public function getColor() : string {
		return $this->color;
	}

	public function isUsed() : bool {
		return $this->used;
	}

	public function setUsed() : bool {
		$this->used = true;

		return $this->used;
	}
}