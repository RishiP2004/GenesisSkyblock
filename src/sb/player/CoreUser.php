<?php

declare(strict_types = 1);

namespace sb\player;

use pocketmine\world\Position;
use sb\player\kit\Kit;
use sb\player\kit\KitHandler;
use sb\player\level\Level;
use sb\player\rank\RankHandler;
use sb\Skyblock;

use sb\database\Database;

use sb\player\rank\Rank;

use pocketmine\permission\Permission;
use pocketmine\Server;

class CoreUser {
	public bool $loaded = false;

	public string $name = "", $ip = "";

	public string $registerDate;

	public int $mobCoins = 0, $money = 0, $essence = 0, $uluru = 0, $kills = 0, $deaths = 0, $killStreak = 0, $slotCredits = 0, $jackpotWins = 0, $jackpotEarnings = 0;

	public ?string $island = "";

	public Rank $rank;

	public array $permissions = [], $homes = [], $kitCds = [], $levels = [];

	public array $rewardTime = [];

	public function __construct(private string $xuid) {}

	public function load(array $data) : void {
		foreach($data as $field => $value) {
			$$field = $value;
		}
		$this->registerDate = $registerDate;
		$this->name = $username;
		$this->ip = $ip;
		$this->mobCoins = $mobCoins;
		$this->money = $money;
		$this->essence = $essence;
		$this->uluru = $uluru;
		$this->rank = RankHandler::get($rank);

		if(!is_null($permissions)) {
			$this->permissions = unserialize($permissions);
		}

		$this->island = is_null($island) ? "" : $island;
		$this->kills = $kills;
		$this->deaths = $deaths;
		$this->killStreak = $killStreak;
		$this->slotCredits = $slotCredits;
		$this->jackpotWins = $jackpotWins;
		$this->jackpotEarnings = $jackpotEarnings;

		if(!is_null($homes)) {
			foreach(json_decode($homes, true) as $name => $data) {
				$this->homes[$name] = json_decode($data, true);
			}
		}
		if(!is_null($levels)) {
			foreach (json_decode($levels, true) as $name => $data) {
				$this->levels[$name] = json_decode($data, true);
			}
		}
		if(!is_null($kitCds)) {
			foreach(json_decode($kitCds, true) as $name => $data) {
				$this->kitCds[$name] = $data;
			}
		}
		if(!is_null($rewardTime)) {
			foreach (json_decode($rewardTime, true) as $name => $data) {
				$this->rewardTime[$name] = $data;
			}
		}
		PlayerManager::getInstance()->coreUsers[$this->getXuid()] = $this;
		$this->setLoaded();
	}

	public function loaded() : bool {
		return $this->loaded;
	}

	public function setLoaded(bool $loaded = true) : void {
		$this->loaded = $loaded;
	}

	public function getXuid() : string {
		return $this->xuid;
	}

	public function getRegisterDate() : string {
		return $this->registerDate;
	}

	public function getName() : string {
		return $this->name;
	}

	public function setName(string $name) : void {
		$this->name = $name;
		$this->save();
	}

	public function getIp() : string {
		return $this->ip;
	}

	public function setIp(string $ip) : void {
		$this->ip = $ip;
		$this->save();
	}

	public function getMobCoins() : int {
		return $this->mobCoins;
	}

	public function setMobCoins(int $mobCoins) : void {
		$this->mobCoins = $mobCoins;
		$this->save();
	}

	public function addMobCoins(int $mobCoins) : void {
		$this->setMobCoins($this->mobCoins += $mobCoins);
	}

	public function reduceMobCoins(int $mobCoins) : void {
		$this->setMobCoins($this->mobCoins -= $mobCoins);
	}

	public function getMoney() : int {
		return $this->money;
	}

	public function setMoney(int $money) : void {
		$this->money = $money;
		$this->save();
	}

	public function addMoney(int $money) : void {
		$this->setMobCoins($this->money += $money);
	}

	public function reduceMoney(int $money) : void {
		$this->setMobCoins($this->money -= $money);
	}

	public function getEssence() : int {
		return $this->essence;
	}

	public function setEssence(int $essence) : void {
		$this->essence = $essence;
		$this->save();
	}

	public function addEssence(int $essence) : void {
		$this->setMobCoins($this->essence += $essence);
	}

	public function reduceEssence(int $essence) : void {
		$this->setMobCoins($this->essence -= $essence);
	}

	public function getUluru() : int {
		return $this->uluru;
	}

	public function setUluru(int $uluru) : void {
		$this->uluru = $uluru;
		$this->save();
	}

	public function addUluru(int $uluru) : void {
		$this->setMobCoins($this->uluru += $uluru);
	}

	public function reduceUluru(int $uluru) : void {
		$this->setMobCoins($this->uluru -= $uluru);
	}

	public function getRank() : Rank {
		return $this->rank;
	}

	public function setRank(Rank $rank) : void {
		$this->rank = $rank;
		$this->save();
	}

	public function getAllPermissions() : array {
		return array_merge($this->getRank()->getPermissions(), $this->permissions);
	}

	public function getPermissions() : array {
		return $this->permissions;
	}

	public function hasPermission(string $permission) : bool {
		if($permission instanceof Permission) {
			$permission = $permission->getName();
		}
		if(Server::getInstance()->isOp($this->getName())) {
			return true;
		}
		if(in_array("*", $this->getAllPermissions())) {
			return true;
		}
		return in_array($permission, $this->getAllPermissions());
	}

	public function setPermissions(array $permissions) : void {
		$this->permissions = $permissions;
		$player = Skyblock::getInstance()->getServer()->getPlayerByPrefix($this->getName());

		if($player instanceof CorePlayer) {
			$player->updatePermissions();
		}
		$this->save();
	}

	public function addPermission(Permission $permission) : void {
		$permissions = array_merge($this->getPermissions(), [$permission->getName()]);

		$this->setPermissions($permissions);
	}

	public function removePermission(Permission $permission) : void {
		$perm = [$permission->getName()];
		$perms = array_diff($this->permissions, $perm);

		$this->setPermissions($perms);
	}

	public function getIsland() : string {
		return $this->island;
	}

	public function setIsland(string $island) : void {
		$this->island = $island;
		$this->save();
	}

	public function hasIsland() : bool {
		return $this->getIsland() !== "";
	}

	public function getKills() : int {
		return $this->kills;
	}

	public function setKills(int $kills) : void {
		$this->kills = $kills;
		$this->save();
	}

	public function getDeaths() : int {
		return $this->deaths;
	}

	public function setDeaths(int $deaths) : void {
		$this->deaths = $deaths;
		$this->save();
	}

	public function getKillStreak() : int {
		return $this->killStreak;
	}

	public function setKillStreak(int $killStreak) : void {
		$this->killStreak = $killStreak;
		$this->save();
	}

	public function getSlotCredits() : int {
		return $this->slotCredits;
	}

	public function setSlotCredits(int $slotCredits) : void {
		$this->slotCredits = $slotCredits;
		$this->save();
	}

	public function increaseSlotCredits(int $slotCredits) : void {
		$this->setSlotCredits($this->slotCredits += $slotCredits);
	}

	public function reduceSlotCredits(int $slotCredits) : void {
		$this->setSlotCredits($this->slotCredits -= $slotCredits);
	}

	public function getJackpotWins() : int {
		return $this->jackpotWins;
	}

	public function setJackpotWins(int $wins) : void {
		$this->jackpotWins = $wins;
		$this->save();
	}

	public function increaseJackpotWins(int $jackpotWins) : void {
		$this->setJackpotWins($this->jackpotWins += $jackpotWins);
	}

	public function getJackpotEarnings() : int {
		return $this->jackpotEarnings;
	}

	public function setJackpotEarnings(int $earnings) : void {
		$this->jackpotEarnings = $earnings;
		$this->save();
	}

	public function increaseJackpotEarnings(int $jackpotEarnings = 1) : void {
		$this->setJackpotEarnings($this->jackpotEarnings += $jackpotEarnings);
	}

	public function getLevelData() : array {
		return $this->levels;
	}

	public function getLevel(Level $level) : int {
		return $this->levels[$level->getName()]["level"] ?? $level->getMinLvl();
	}

	public function setLevel(Level $level, int $val) {
		$this->levels[$level->getName()]["level"] = $val;
		$this->save();
	}

	public function getLevelXp(Level $level) : int {
		return $this->levels[$level->getName()]["xp"] ?? $level->getMinLvl();
	}

	public function setLevelXp(Level $level, int $val) {
		$this->levels[$level->getName()]["xp"] = $val;
		$this->save();
	}

	public function getHomes(): array
	{
		return $this->homes;
	}

	public function getHome(string $name): ?Position {
		$d = $this->homes[$name] ?? null;
		if ($d === null) return null;

		return new Position($d['x'], $d['y'], $d['z'], Server::getInstance()->getWorldManager()->getWorldByName($d['world']));
	}

	public function createHome(string $name, Position $pos) : void {
		$this->homes[$name] = ['x' => $pos->getX(), 'y' => $pos->getY(), 'z' => $pos->getZ(), 'world' => $pos->getWorld()->getFolderName()];
		$this->save();
	}

	public function getHomeNames() : array {
		return array_keys($this->homes);
	}

	public function deleteHome(string $name) : void {
		unset($this->homes[$name]);
		$this->save();
	}

	public function getKitCds() : array {
		return $this->kitCds;
	}

	public function getKitCd(Kit $kit) : int {
		if(isset($this->kitCds[$kit->getName()])){
			return $kit->getCooldown() -  (time() - $this->kitCds[$kit->getName()]);
		}
		return 0;
	}

	public function setKitCd(Kit $kit): void{
		$this->kitCds[$kit->getName()] = time();
		$this->save();
	}

	public function hasKitCd(Kit $kit) : bool {
		return $this->getKitCd($kit) > 0;
	}

	public function hasKit(Kit $kit): bool{
		return $this->hasPermission($kit->getPermission());
	}


	public function getRewardTime() : array {
		return $this->rewardTime;
	}

	public function getRewardTimeFor(string $type) : ?int {
		if(isset($this->rewardTime[$type])){
			return $this->rewardTime[$type];
		}
		return null;
	}

	public function setRewardTime(string $type, int $time) : void {
		$this->rewardTime[$type] = $time;
		$this->save();
	}

	public function save() : void {
		$homes = [];
		if(!empty($this->getHomes())) {
			foreach ($this->getHomes() as $name => $data) {
				$homes[$name] = json_encode($data);
			}
		}
		$levels = [];
		foreach ($this->levels as $name => $data) {
			$levels[$name] = json_encode($data);
		}
		Database::get()->executeChange("player.update", [
			"username" => $this->getName(),
			"ip" => $this->getIp(),
			"mobCoins" => $this->getMobCoins(),
			"money" => $this->getMoney(),
			"essence" => $this->getEssence(),
			"uluru" => $this->getUluru(),
			"rank" => $this->getRank()->getName(),
			"permissions" => serialize($this->getPermissions()),
			"island" => $this->getIsland() ?? "",
			"kills" => $this->getKills(),
			"deaths" => $this->getDeaths(),
			"killStreak" => $this->getKillStreak(),
			"slotCredits" => $this->getSlotCredits(),
			"jackpotWins" => $this->getJackpotWins(),
			"jackpotEarnings" => $this->getJackpotEarnings(),
			"homes" => json_encode($homes),
			"kitCds" => json_encode($this->getKitCds()),
			"levels" => json_encode($levels),
			"rewardTime" => json_encode($this->getRewardTime()),
			"xuid" => $this->getXuid()
		]);
	}
	//Use with caution.
	public function unregister() : void {
		//remove from island?
		Database::get()->executeChange("player.delete", [
			"xuid" => $this->getXuid()
		]);
		unset(PlayerManager::getInstance()->coreUsers[$this->getXuid()]);
	}
}