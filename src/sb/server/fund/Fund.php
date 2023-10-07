<?php
namespace sb\server\fund;
use pocketmine\utils\TextFormat as C;
use JetBrains\PhpStorm\ArrayShape;
use sb\utils\MathUtils;

class Fund{

	public function __construct(public readonly string $name, public readonly string $formButton, public int $progress = 0, public int $goal = 0, public readonly array $forbiddenCommands = [], public readonly array $description = []){}

	public function __toString(): string{
		return $this->name;
	}

	public function getProgress(): int{
		return $this->progress;
	}

	public function isCompleted(): bool{
		return $this->progress >= $this->goal;
	}

	public function getPercentage(): string{
		$percent = ($this->progress / $this->goal) * 100;

		if ($this->getProgress() >= $this->getGoal()) {
			return "100.00";
		}
		return number_format($percent, 2);
	}

	public function getProgressBar(): string {
		$percentage = $this->progress / $this->goal;

		$bar = "";
		for ($i = 0; $i < 20; $i++) {
			if ($i < $percentage * 20) {
				$bar .= C::GREEN . "█";
			} else {
				$bar .= C::GRAY . "█";
			}
		}
		return $bar . C::BOLD . C::GOLD . " $" . MathUtils::intToPrefix($this->getGoal());
	}

	public function addProgress(int $amount): void{
		$this->progress += $amount;
	}

	public function getGoal(): int{
		return $this->goal;
	}

	public function getFormButton(): string{
		return $this->formButton;
	}

	public function getForbiddenCommands(): array{
		return $this->forbiddenCommands;
	}

	public function getDescription(): array{
		return $this->description;
	}

	#[ArrayShape(["progress" => "int", "max" => "int", "blocked-commands" => "array", "description" => "array"])] public function toArray(): array {
		return [
			"progress" => $this->progress,
			"goal" => $this->goal,
			"blocked-commands" => $this->forbiddenCommands,
			"description" => $this->description,
			"form-button" => $this->formButton,
		];
	}
}