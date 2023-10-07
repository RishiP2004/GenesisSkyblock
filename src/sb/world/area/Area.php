<?php

namespace sb\world\area;

use pocketmine\math\AxisAlignedBB;
use pocketmine\world\Position;
use sb\utils\MathUtils;

class Area {
	private AxisAlignedBB $aaBB;

	public function __construct(private readonly string $name, private readonly Position $pos1, private readonly Position $pos2, private readonly bool $pvpFlag, private readonly bool $editFlag, private readonly string $title, private readonly string   $msg) {
		$this->recalculateBoundingBox();
	}

	public function recalculateBoundingBox(): void {
		$this->aaBB = MathUtils::fromCoordinates($this->pos1->asVector3(), $this->pos2->asVector3());
		$this->aaBB->expand(0.0001, 0.0001, 0.0001);
		$this->aaBB->maxX += 1;
		$this->aaBB->maxZ += 1;
	}

	public function isPositionInside(Position $position) {
		return $this->aaBB->isVectorInside($position->floor());
	}

	public function getPos1(): Position {
		return $this->pos1;
	}

	public function getPos2(): Position {
		return $this->pos2;
	}

	public function getName(): string {
		return $this->name;
	}

	public function getPvpFlag(): bool {
		return $this->pvpFlag;
	}

	public function getEditFlag(): bool {
		return $this->editFlag;
	}

	/**
	 * @return string
	 */
	public function getTitle() : string {
		return $this->title;
	}

	public function getMsg() : string {
		return $this->msg;
	}
}