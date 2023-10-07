<?php

declare(strict_types = 1);

namespace sb\player;

use jackmd\scorefactory\ScoreFactory;
use pocketmine\inventory\Inventory;
use pocketmine\item\VanillaItems;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionAttachment;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use sb\islands\Island;
use sb\islands\traits\IslandCallTrait;
use sb\item\CustomItem;
use sb\item\CustomItems;
use sb\item\Lootbox;
use sb\item\PermNote;
use sb\item\sets\SetArmor;
use sb\item\utils\LootboxType;
use sb\player\kit\Kit;
use sb\player\kit\types\free\EarthKit;
use sb\player\kit\types\free\JupiterKit;
use sb\player\kit\types\free\MarsKit;
use sb\player\kit\types\free\MemberKit;
use sb\player\kit\types\free\MercuryKit;
use sb\player\kit\types\free\SaturnKit;
use sb\player\level\LevelHandler;
use sb\scheduler\player\RollVoteCrateTask;
use sb\utils\MathUtils;
use sb\world\koth\Koth;
use sb\Skyblock;
use sb\utils\PMUtils;
use sb\world\WorldManager;
use sb\scheduler\player\RollSlotbotTask;

class CorePlayer extends Player {
	use IslandCallTrait;

	public ?CoreUser $coreUser = null;

	const STAFF_CHAT = "staff";
	const NORMAL_CHAT = "normal";
	public int $lastHeldSlot = 0; //offset

	private array $interacts = [];
	private int $chatTime = 0;

	public string $chatType = self::NORMAL_CHAT;
	public bool $fly = false;

	public bool $staffMode = false;

	public ?PermissionAttachment $attachment = null;

	public ?string $islandName = ""; //temp islandCache for island name, string or null

	public array $islandInvites = [];

	private ?int $combatTag = 0;

	public ?string $armor = null;

	public int $slotbotRunningTime = 0;
	public bool $slotBotRunning = false;
	public bool $playerIsTeleporting = false;

	public bool $voteCrateRunning = false;

	public ?Koth $koth = null;

	public function getCoreUser() : CoreUser {
		if(!$this->isInitialized()) {
			throw new \RuntimeException("Tried to get core user of uninitialized player");
		}
		return $this->coreUser;
	}

	public function initialize(CoreUser $user): void{
		if(!$this->isInitialized()) {
			$this->coreUser = $user;
		}

	}

	public function join(CoreUser $coreUser) : void {
		$this->initialize($coreUser);

		if($this->isOnline()) {
			$this->attachment = $this->addAttachment(Skyblock::getInstance());
			$this->updatePermissions();
			$this->initScoreboard();
			WorldManager::getInstance()->spawnNPCs($this);
			WorldManager::getInstance()->spawnHolograms($this);

			if($this->getCoreUser()->getName() !== $this->getName()) {
				$this->getCoreUser()->setName($this->getName());
			}
			$this->setNameTag($this->getCoreUser()->getRank()->getNameTagFormatFor($this));
			$this->setScoreTag("§r§f" . $this->getHealth() . TextFormat::RED . " ❤");

			$this->getIsland($this->getCoreUser()->getIsland(), function($island) {
				if(is_null($island)) {
					$this->islandName = "";
				} else {
					$this->islandName = $island->getName();
				}
			});
		}
	}

	public function getIslandName() : ?string {
		return $this->islandName;
	}

	public function getIslandInvites() : array {
		return $this->islandInvites;
	}

	public function hasIslandInvite(string $island) {
		return in_array($island, $this->islandInvites);
	}

	public function clearIslandInvites() : void {
		$this->islandInvites = [];
	}

	public function addIslandInvite(string $island) {
		$this->islandInvites[$island] = $island;
	}


	public function removeIslandInvite(string $island) {
		unset($this->islandInvites[strtolower($island)]);
	}

	public function hasPermission(Permission|string $permission) : bool {
		if(parent::hasPermission($permission)) {
			return true;
		}
		$rank = $this->getCoreUser()->getRank();
		if($rank->hasPermission($permission)) {
			return true;
		}
		if($permission instanceof Permission) {
			$permission = $permission->getName();
		}
		if(Server::getInstance()->isOp($this->getName())) {
			return true;
		}
		if(in_array("*", $this->getCoreUser()->getAllPermissions())) {
			return true;
		}
		return in_array($permission, $this->getCoreUser()->getAllPermissions());
	}

	public function leave() : void {
		if($this->isCombatTagged()) $this->kill();
		WorldManager::getInstance()->despawnNPCs($this);

		if($this->isInitialized()) {
			$this->getCoreUser()->save();

			if(property_exists($this,"attachment")) { //cuz initialization sometimes.
				if(!is_null($this->getAttachment())) {
					$this->removeAttachment($this->getAttachment());
				}
			}
		}
	}

	public function isTeleporting() : bool {
		return $this->playerIsTeleporting;
	}

	public function setTeleporting(bool $value) : void {
		$this->playerIsTeleporting = $value;
	}

	public function isInitialized() : bool {
		return $this->coreUser instanceof CoreUser;
	}

	public function flying() : bool {
		return $this->fly;
	}
	//todo?
	public function setFly() : void {
		$this->fly = !$this->fly;

		$this->setAllowFlight($this->fly);
		$this->setFlying($this->fly);

	}

	//better way?
	public function isInStaffMode() : bool {
		return $this->staffMode;
	}

	public function setStaffMode(bool $value) : void {
		$this->staffMode = $value;

		switch ($value){
			case true:
			case false:
				break;
		}
	}

	public function getAttachment() : ?PermissionAttachment {
		return $this->attachment;
	}

	public function hasKitCD(Kit $kit): bool{
		return $this->getCoreUser()->hasKitCd($kit);
	}

	public function hasKit(Kit $kit): bool{
		return $this->getCoreUser()->hasKit($kit);
	}

	public function getKitCD(Kit $kit): int{
		return $this->getCoreUser()->getKitCd($kit);
	}

	public function setKitCd(Kit $kit): void{
		$this->getCoreUser()->setKitCd($kit);
	}
	
	public function setIslandName(string $name) : void {
		$this->islandName = $name;
	}

	public function updatePermissions() : void {
		if(!$this->isInitialized()) {
			return;
		}
		$attachment = $this->getAttachment();
		$attachment->clearPermissions();

		foreach($this->getCoreUser()->getAllPermissions() as $permission) {
			if($permission === "*") {
				foreach(PMUtils::getPocketMinePermissions() as $perm) {
					$attachment->setPermission($perm, true);
				}
			} else if(is_string($permission)) {
				$attachment->setPermission($permission, true);
			}
		}
	}
	//todo: in future clean up
	public function rollSlotBot(array $slots, Inventory $inventory) : void {
		$this->slotBotRunning = true;
		Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new RollSlotbotTask($this, $inventory, $slots), 5);
	}

	public function rollVoteCrate() : void {
		$this->voteCrateRunning = true;
		Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new RollVoteCrateTask($this, [VanillaItems::IRON_SWORD(), VanillaItems::DIAMOND_PICKAXE(), VanillaItems::APPLE()]), 1);
	}

	public function initScoreboard() : void {
		ScoreFactory::setObjective($this, TextFormat::colorize("&r&l&bSaturn"));
		ScoreFactory::sendObjective($this);
	}
	// 0-8 lines
	public function getDefaultLines(): array{
		return [
			"&l&3┌ " . $this->getName(),
			"&l&3 | &r&bBalance: &7$" . number_format($this->getCoreUser()->getMoney()),
			"&l&3 | &r&bMob Coins: &7" . number_format($this->getCoreUser()->getMobCoins()),
			"&l&3 | &r&bUluru: &7" . number_format($this->getCoreUser()->getUluru()),
			"&l&3 | &r&bEssence: &7" . number_format($this->getCoreUser()->getEssence()),
			"&l&3 |- Status",
			"&l&3 | &r&bFarming Level: &7" . number_format($this->getCoreUser()->getLevel(LevelHandler::get("farming"))),
			"&l&3 | &r&bKDR: &7" . $this->getCoreUser()->getKills() . ":" . $this->getCoreUser()->getDeaths() . " (" . $this->getCoreUser()->getKillStreak() . ")",
			"&l&3 | &r&bVote Party: &70/50",
		];
	}
	//from 9-11
	public function getIslandLines(?Island $island) : array {
		if(is_null($island)) {
			return [
				"&l&3 |- Island",
				"&l&3 | &r&bType /is create.",
				"&l&3 | &r&bOr join a island.",
				"&l&3 L"
			];
		} else {
			return [
				"&l&3 |- Island",
				"&l&3 | &r&bValue: &7$" . number_format($island?->getValue() ?? 0),
				//"&l&3 | &r&bPower: &7100" . number_format($island?->getPower() ?? 0),
				"&l&3 | &r&bMembers: &7" . number_format(count($island?->getMembers())),
				"&l&3 L"
			];
		}
	}
	//from 9-15
	public function getKothLines(Koth $koth) : array {
		$newKing = $koth->getCurrentKing()[0]->getName() ?? "None";
		$cTime = $koth->getCurrentKing()[1] ?? $koth->getWinTime();
		
		return [
			"&l&3 |- KoTH: &l&4" . $koth->getName(),
			"&l&3 | &r&bKing: &7" . $newKing,
			"&l&3 | &r&b(" . MathUtils::secondsToTime((int) $cTime)["m"] . " m" . MathUtils::secondsToTime((int) $cTime)["s"] . " s)",
			"&l&3 |",
			"&l&3 | &r&b&7Your time: " . MathUtils::secondsToTime((int) $koth->getTimers()[$this->getName()])["m"] . " m" . MathUtils::secondsToTime((int) $koth->getTimers()[$this->getName()])["s"] . " s",
			"&l&3 L"
		];
	}

	public function getKoth() : ?Koth {
		return $this->koth;
	}

	public function setKoth(?Koth $koth) : void {
		$this->koth = $koth;
		$this->resetLines();
	}

	public function resetLines() : void {
		ScoreFactory::removeObjective($this);
		$this->initScoreboard();
	}

	public function updateScoreboardLines() : void {
		if (!ScoreFactory::hasObjective($this)) {
			return;
		}
		$this->getIsland($this->getCoreUser()->getIsland(), function($island) {
			ScoreFactory::setScoreLine($this, 0, TextFormat::colorize("&7" . date("H:i A", strtotime(date("m/d/Y H:i:s"))) . " &8|&7 " . date("m/d/Y")));
			ScoreFactory::setScoreLine($this, 15, TextFormat::colorize("      &r&o&7genesispvp.net      "));
			$islandLines = $this->getIslandLines($island);
			$defaultLines = $this->getDefaultLines();

			if($this->getKoth() != null) {
				$lines = [
					...$islandLines,
					...$this->getKothLines($this->getKoth())
				];
			} else {

				$lines = [
					...$defaultLines,
					...$islandLines,
				];
			}
			$current = 0;

			foreach($lines as $line) {
				ScoreFactory::setScoreLine($this, $current, TextFormat::colorize($line));
				$current++;
			}
			ScoreFactory::sendLines($this);
		});
	}

	public function wearFullArmorSet(): ?SetArmor{
		$armor = [];

		foreach ($this->getArmorInventory()->getContents() as $slot => $item) {
			if(($string = $item->getNamedTag()->getString(CustomItem::TAG_CUSTOM_ITEM, "")) !== "") {
				/**
				 * @var CustomItem $item
				 */
				$item = CustomItems::fromString($string);

				if ($item instanceof SetArmor) {
					$id = explode("_", $item->getId())[0];
					if (!isset($armor[$id])) $armor[$id] = 0;
					$armor[$id] += 1;
				}
			}
		}
		foreach ($armor as $id => $count) {
			if ($count >= 4) return CustomItems::fromString($id . "_Helmet"); //hacky fix
		}
		return null;
	}

	public function isCombatTagged() : bool{
		return $this->combatTag != null;
	}

	public function combatTag(bool $value = true) : void {
		$this->combatTag = $value ? 15 : null;
	}

	public function setCombatTagTime(int $value) : void {
		$this->combatTag = $value;
	}

	public function getCombatTagTime() : int {
		return $this->combatTag;
	}
}
