<?php

namespace sb\islands;

use pocketmine\utils\TextFormat;
use sb\islands\utils\IslandStats;
use sb\islands\upgrades\IslandHopperLimitUpgrade;
use sb\islands\upgrades\IslandSpawnerLimitUpgrade;
use sb\lang\CustomKnownTranslationFactory;
use sb\player\traits\PlayerCallTrait;
use sb\scheduler\player\TeleportTimerTask;
use sb\Skyblock;
use Symfony\Component\Filesystem\Path;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;
use sb\database\IslandDB;
use sb\islands\utils\IslandMember;
use sb\permission\utils\IslandPermissions;
use sb\islands\utils\IslandRole;
use sb\islands\upgrades\IslandMemberSizeUpgrade;
use sb\islands\upgrades\IslandSizeUpgrade;
use sb\islands\upgrades\IslandUpgrade;
use sb\player\CorePlayer;
use sb\player\CoreUser;
use sb\scheduler\server\FileDeleteAsyncTask;

class Island {
	use PlayerCallTrait;

	public string $name;

	public string $leader;

	public string $type;

	/** @var array<string, IslandMember> */
	public array $members = [];
	public array $invites = [];

	/** @var array<string, int> */
	public array $stats = [];

	/** @var array<string, IslandRole> */
	public array $roles = [];

	public Vector3 $spawn;

	public string $defaultRole = "Member";

	public bool $locked = true;

	/** @var array<string, IslandUpgrade> */
	public array $upgrades = [];

	public int $value = 0, $power = 0, $xp = 0, $level = 0;

	public function __construct(
		private readonly string $id
	) {}

	public function load(array $data) : void {
		foreach($data as $field => $val) {
			$$field = $val;
		}
		$this->leader = $leader;
		$this->type = $type;

		$membersArr = [];

		foreach(json_decode($members) as $member) {
			$memberDe = IslandMember::unSerialize($member);
			$membersArr[$memberDe->getName()] = $memberDe;
		}
		$this->members = $membersArr;
		$rolesArr = [];
		foreach(json_decode($roles) as $role) {
			$roleDe = IslandRole::unSerialize($role);
			$rolesArr[$roleDe->getName()] = $roleDe;
		}
		$this->roles = $rolesArr;

		$spawnDe = unserialize($spawn);
		$this->spawn = new Vector3($spawnDe["x"], $spawnDe["y"], $spawnDe["z"]);

		$this->stats = unserialize($stats);

		$this->name = $name;
		$this->defaultRole = $defaultRole;
		$this->locked = $locked;

		$upgradeArr = [];
		foreach(json_decode($upgrades) as $upgrade) {
			$upgradeDe = IslandUpgrade::unserialize($upgrade);
			$upgradeArr[$upgradeDe->getName()] = $upgradeDe;
		}
		$this->upgrades = $upgradeArr;
		$this->value = $value;
		$this->power = $power;
		$this->xp = $xp;
		$this->level = $level;

		IslandManager::getInstance()->islands[$this->getId()] = $this;
	}

	public function setupDefaults() : void {
		$this->roles["Owner"] = new IslandRole("Owner", [IslandPermissions::PERMISSION_ALL], false);
		$this->roles["Member"] = new IslandRole("Member", []);

		$this->setUpgrade(new IslandSizeUpgrade(0, 0, $this->getId()));
		$this->setUpgrade(new IslandMemberSizeUpgrade(0, 0, $this->getId()));
		$this->setUpgrade(new IslandHopperLimitUpgrade(0, 0, 0, $this->getId()));
		$this->setUpgrade(new IslandSpawnerLimitUpgrade(0, 0, 0, $this->getId()));

		$this->stats[IslandStats::FISH_COUNT] = 0;
		$this->stats[IslandStats::BLOCKS_MINED] = 0;
		$this->stats[IslandStats::MOBS_KILLED_AFK] = 0;
		$this->stats[IslandStats::MOBS_KILLED_MANUALLY] = 0;
		$this->stats[IslandStats::CROPS_HARVESTED] = 0;


		$this->members[$this->getLeader()] = new IslandMember($this->getLeader(), "Owner");
	}

	public function getName() : string {
		return $this->name;
	}

	public function setName(string $name) : void {
		$this->name = $name;
		$this->save();
	}

	public function getId() : string {
		return $this->id;
	}

	public function getType() : string {
		return $this->type;
	}

	public function getLeader() : string {
		return $this->leader;
	}

	public function setLeader(CorePlayer $leader) : void {
		$this->leader = $leader->getName();
		$this->save();
	}

	public function getMembers() : array {
		return $this->members;
	}

	public function isMember(CoreUser $user) : bool {
		return is_null($this->getMember($user));
	}

	public function getMember(CoreUser $user) : ?IslandMember {
		return $this->members[$user->getName()] ?? null;
	}

	public function addMember(CorePlayer $member): void {
		$this->members[$member->getName()] = new IslandMember($member->getName(), $this->getDefaultRole());

		$member->getCoreUser()->setIsland($this->getId());
		$this->save();
	}

	public function removeMember(IslandMember $kicked): void {
		unset($this->members[$kicked->getName()]);


		/** @var CorePlayer $player */
		$player = Server::getInstance()->getPlayerExact($kicked->getName());
		$player?->setIslandName("");
		$player->getCoreUser()->setIsland("");
		$player->save();

		$this->save();
	}

	public function getInvited() : array {
		return $this->invites;
	}

	public function isInvited(CorePlayer $player) : bool {
		return in_array($player->getName(), $this->invites);
	}

	public function invite(CorePlayer $player) : void {
		$this->invites[] = $player->getName();

		$player->addIslandInvite($this->getName());
		$this->save();
	}

	public function uninvite(CorePlayer $player) : void {
		unset($this->invites[$player->getName()]);
		$player->removeIslandInvite($this->getName());
		$this->save();
	}

	public function getRoles() : array {
		return $this->roles;
	}

	public function getRole(CoreUser $user) : IslandRole {
		return $this->roles[$this->members[$user->getName()]->getRole()];
	}

	public function getRoleRaw(string $role) : ?IslandRole {
		return $this->roles[$role] ?? null;
	}
	//for the actual Role names itself. Set a user's role through member object.
	public function createRole(string $name, IslandRole $role) : void {
		$this->roles[$name] = $role;
		$this->save();
	}

	public function deleteRole(string $name) : void {
		unset($this->roles[$name]);
	}

	public function getWorld() : ?World {
		return Server::getInstance()->getWorldManager()->getWorldByName($this->getWorldName());
	}

	public function getWorldName() : string {
		return "is-" . $this->getName();
	}

	public function getWorldDir() : string {
		return Path::join(Server::getInstance()->getDataPath(), 'worlds', "is-" . $this->getName());
	}

	public function isWorldLoaded() : bool {
		$world = $this->getWorld();
		return !is_null($world) && $world->isLoaded();
	}

	public function loadWorld() : void {
		Server::getInstance()->getWorldManager()->loadWorld($this->getWorldName());
	}

	public function unloadWorld() : void {
		Server::getInstance()->getWorldManager()->unloadWorld($this->getWorld());
	}

	public function getSpawn() : Vector3 {
		return $this->spawn;
	}

	public function setSpawn(Vector3 $spawn) : void {
		$this->spawn = $spawn;
		$this->save();
	}

	public function teleport(CorePlayer $player, bool $speed = false) : void {
		if (!$this->isWorldLoaded()) {
			$this->loadWorld();
		}
		$world = $this->getWorld();

		if(!$speed) {
			$vec = $this->getSpawn();
		} else $vec = $world->getSpawnLocation();
		$timer = 7;

		$pos = new Position($vec->getX(), $vec->getY(), $vec->getZ(), $this->getWorld());

		$world->orderChunkPopulation($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4, null)->onCompletion(function() use($player, $world, $pos, $timer, $speed) : void {
			if($player !== null) {
				if($speed) {
					$player->teleport($pos);
					$player->sendMessage(CustomKnownTranslationFactory::island_teleported());
					$this->setSpawn($world->getSpawnLocation());
					return;
				}
				Skyblock::getInstance()->getScheduler()->scheduleRepeatingTask(new TeleportTimerTask($player, $pos, $timer), 20);
			}
		}, function() : void{
		});
	}

	public function setDefaultRole(string $new) : void {
		$this->defaultRole = $new;
		$this->save();
	}

	public function getDefaultRole() : string {
		return $this->defaultRole;
	}

	public function setLocked(bool $locked) : void {
		$this->locked = $locked;
		$this->save();
	}

	public function isLocked() : bool {
		return $this->locked;
	}

	public function getUpgrade(string $name) : IslandUpgrade {
		return $this->upgrades[$name];
	}

	public function setUpgrade(IslandUpgrade $upgrade) : void {
		$this->upgrades[$upgrade->getName()] = $upgrade;
	}

	public function getUpgrades() : array {
		return $this->upgrades;
	}

	public function handleBlockBreak(BlockBreakEvent $event) : void {
		foreach($this->upgrades as $upgrade) {
			$upgrade->interceptBreak($event);
		}
		if(!$event->isCancelled()) $this->addStat(IslandStats::BLOCKS_MINED, 1);
	}

	public function handleBlockPlace(BlockPlaceEvent $event) : void {
		foreach($this->upgrades as $upgrade) {
			$upgrade->interceptPlace($event);
		}
	}

	public function getLevel() : int {
		return $this->level;
	}

	public function levelUp() : void {
		$this->level++;
		$this->announce("Island §6LEVELED UP §rto level §6" . $this->getLevel());
		$this->save();
	}

	public function getXp() : int {
		return $this->xp;
	}

	public function addXp(int $amount) : void {
		$this->xp += $amount;
		$this->save();
	}

	public function setXp(int $amount) : void {
		$this->xp = $amount;
		$this->save();
	}

	public function getStats() : array {
		return $this->stats;
	}

	public function getStat(string $name) : int {
		return $this->stats[$name];
	}

	public function setStat(string $stat, int $amount) : void {
		$this->stats[$stat] = $amount;
		$this->save();
	}

	public function addStat(string $stat, int $amount) : void {
		$this->stats[$stat] += $amount;
		$this->save();
	}

	public function addValue(int $amount) : void {
		$this->value += $amount;
		$this->save();
	}

	public function removeValue(int $amount) : void {
		$this->value -= $amount;
		$this->save();
	}

	public function getValue() : int {
		return $this->value;
	}

	public function addPower(int $amount) : void {
		$this->power += $amount;
		$this->save();
	}

	public function removePower(int $amount) : void {
		$this->power -= $amount;
		$this->save();
	}

	public function getPower() : int {
		return $this->power;
	}

	public function delete() : void {
		IslandDB::get()->executeChange("islands.delete", [
			"id" => $this->getId()
		]);
		if($this->isWorldLoaded()) {
			$this->getWorld()->getServer()->getWorldManager()->unloadWorld($this->getWorld());
			Server::getInstance()->getAsyncPool()->submitTask(new FileDeleteAsyncTask($this->getWorldDir(), function() {}));
		}
		$this->announce("&r&c&l(!) &r&cYour team's island has been deleted by the leader.");
		$this->announce("&r&7You may create a personal island with &r&7/is create");
		foreach($this->getMembers() as $member) {
			$this->getCoreUser($member->getName(), function($user) use ($member) {
				if(is_null($user)) {
					//weird...
					return false;
				}
				$user->setIsland("");
				return true;
			});
			$p = Server::getInstance()->getPlayerExact($member->getName());

			if($p instanceof CorePlayer) {
				$p->teleport(Server::getInstance()->getWorldManager()->getWorldByName("world")->getSpawnLocation());
				$p->islandName = null;
			}
		}
		unset(IslandManager::getInstance()->islands[$this->getId()]);
	}

	public function getMaxMembers() : int {
		var_dump($this->getUpgrade("Member Size")->getMaxMembers());
		return $this->getUpgrade("Member Size")->getMaxMembers();
	}

	public function announce(string $message) : void {
		foreach($this->getMembers() as $member) {
			$p = Server::getInstance()->getPlayerExact($member->getName());

			if($p instanceof CorePlayer) $p->sendMessage(TextFormat::colorize($message));
		}
	}

	public function save() : void {
		$members = [];
		foreach($this->getMembers() as $member) {
			$members[] = $member->serialize();
		}
		$roles = [];
		foreach($this->getRoles() as $role) {
			$roles[] = $role->serialize();
		}
		$spawnEn = serialize([
			"x"=>$this->getSpawn()->x,
			"y"=>$this->getSpawn()->y,
			"z"=>$this->getSpawn()->z
		]);
		$upgrades = [];
		foreach($this->getUpgrades() as $upgrade) {
			$upgrades[] = $upgrade->serialize();
		}
		$stats = serialize($this->getStats());

		IslandDB::get()->executeChange("islands.update", [
			"name" => $this->getName(),
			"leader" => $this->getLeader(),
			"members" => json_encode($members),
			"roles" => json_encode($roles),
			"spawn" => $spawnEn,
			"defaultRole" => $this->getDefaultRole(),
			"locked" => $this->isLocked(),
			"upgrades" => json_encode($upgrades),
			"value" => $this->getValue(),
			"power" => $this->getPower(),
			"xp" => $this->getXp(),
			"level" => $this->getLevel(),
			"stats" => $stats,
			"id" => $this->getId()
		]);
	}
}