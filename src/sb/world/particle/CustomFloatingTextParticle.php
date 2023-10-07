<?php

namespace sb\world\particle;

use pocketmine\Server;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\Position;
use sb\player\CorePlayer;
//fix?
class CustomFloatingTextParticle extends FloatingTextParticle {
	private array $viewers = [];

	public function __construct(
		private readonly string $identifier,
		private Position $pos,
		private string $message,
	) {
		parent::__construct("", "");
		$this->update();
	}

	public function getMessage(): string {
		return $this->message;
	}

	public function update(?string $message = null): void {
		$this->message = $message ?? $this->message;
	}

	public function getIdentifier(): string {
		return $this->identifier;
	}

	public function getPos() : Position {
		return $this->pos;
	}

	//todo: check for duplications
	public function sendChangesToAll(): void {
		foreach(Server::getInstance()->getOnlinePlayers() as $player) {
			$this->updateFor($player);
		}
	}

	public function move(Position $position) {
		$this->pos = $position;
	}

	public function updateFor(CorePlayer $player): void {
		$this->setTitle($this->message);
		$world = $player->getWorld();

		if($world === null) return;
		if($this->getPos()->getWorld()->getDisplayName() !== $world->getDisplayName()) return;

		$this->getPos()->getWorld()->addParticle($this->getPos(), $this, [$player]);
	}

	public function spawn(CorePlayer $player): void {
		$this->setInvisible(false);
		$level = $player->getWorld();
		if($level === null) {
			return;
		}
		$this->getPos()->getWorld()->addParticle($this->position, $this, [$player]);
	}

	public function despawnFrom(CorePlayer $player): void {
		$this->setInvisible();
		$world = $player->getWorld();
		if($world === null) return;

		$this->getPos()->getWorld()->addParticle($this->getPos(), $this, [$player]);
	}
}
