<?php

namespace sb\server\fund;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\SingletonTrait;
use sb\Skyblock;

class FundHandler{
	use SingletonTrait;
	/** @var Fund[] $funds */
	private array $funds = [];
	const TITLE = C::RESET . C::BOLD . C::GOLD . "<" . C::YELLOW . "x" . C::GOLD . "> " . C::RESET . C::BOLD . C::GOLD . "Genesis World Fund" . C::RESET . C::BOLD . C::GOLD . " <" . C::YELLOW . "x" . C::GOLD . ">";

	public function __construct(private Skyblock $core){
		self::setInstance($this);
		$fundConfig = new Config($this->core->getDataFolder() . "funds.yml", Config::YAML);

		foreach ($fundConfig->get("funds", []) as $name => $fundData) {
			if (isset($this->funds[$name])) continue;

			try {
				$formButton = $fundData["form_button"] ?? null;
				$progress = $fundData["progress"] ?? 0;
				$goal = $fundData["goal"] ?? 0;
				$forbiddenCommands = $fundData["blocked-commands"] ?? [];
				$description = $fundData["description"] ?? [];

				if (!$formButton) {
					throw new \InvalidArgumentException("Missing 'form-button' for fund '$name'");
				}
				if(!$goal){
					throw new \InvalidArgumentException("Missing 'goal' for fund '$name'");
				}

				$this->funds[$name] = new Fund($name, $formButton, $progress, $goal, $forbiddenCommands, $description);
			} catch (\Exception $e) {
				Skyblock::getInstance()->getLogger()->error("Failed to load fund $name: " . $e->getMessage());
				continue;
			}
		}
	}

	public function saveAll(): void{
		$fundConfig = new Config($this->core->getDataFolder() . "funds.yml", Config::YAML);
		$fundConfig->set("funds", array_map(function(Fund $fund): array{
			return [
				"form_button" => $fund->getFormButton(),
				"progress" => $fund->getProgress(),
				"goal" => $fund->getGoal(),
				"blocked-commands" => $fund->getForbiddenCommands(),
				"description" => $fund->getDescription()
			];
		}, $this->funds));
		$fundConfig->save();
	}

	public function getFunds(): array{
		return $this->funds;
	}

	public function getFund(string $name): ?Fund{
		return $this->funds[$name] ?? null;
	}
}
