<?php

declare(strict_types = 1);

namespace sb\player;

use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use sb\command\player\DeleteHomeCommand;
use sb\command\player\KitCommand;
use sb\command\player\RewardsCommand;
use sb\command\player\SpawnCommand;
use sb\command\player\CustomItemCommand;
use sb\command\player\staff\FlyCommand;
use sb\command\player\HomeCommand;
use sb\command\player\ListHomesCommand;
use sb\command\player\PingCommand;
use sb\command\player\SetHomeCommand;
use sb\command\player\SetsCommand;
use sb\command\player\staff\AddEssenceCommand;
use sb\command\player\staff\AddMoneyCommand;
use sb\command\player\staff\AddUluruCommand;
use sb\command\player\BalanceCommand;
use sb\command\player\PayCommand;
use sb\command\player\staff\ReduceEssenceCommand;
use sb\command\player\staff\ReduceMobCoinsCommand;
use sb\command\player\staff\ReduceMoneyCommand;
use sb\command\player\staff\ReduceUluruCommand;
use sb\command\player\staff\SetEssenceCommand;
use sb\command\player\staff\SetMoneyCommand;
use sb\command\player\staff\SetUluruCommand;
use sb\command\player\SlotBotCommand;
use sb\command\player\staff\StaffModeCommand;
use sb\command\player\staff\TicketCommand;
use sb\command\player\TopMoneyCommand;
use sb\command\player\WarpCommand;
use sb\database\Database;
use sb\player\ability\AbilityManager;
use sb\player\p2p\CoinflipHandler;
use sb\player\kit\KitHandler;
use sb\player\level\LevelHandler;
use sb\player\rank\RankHandler;
use sb\Skyblock;
use sb\command\player\TradeCommand;

class PlayerManager implements PlayerData {
	use SingletonTrait;

	public array $coreUsers = [];

	private array $rewardItems = [];

    public function __construct() {
    	self::$instance = $this;

        Database::get()->executeGeneric("player.init");
		new RankHandler();
		new KitHandler();
		new CoinflipHandler();
		new AbilityManager();
		new LevelHandler();
    }

	public function tick() : void {
		foreach(Server::getInstance()->getOnlinePlayers() as $session) {
			if($session->isInitialized()) {
				$session->updateScoreboardLines();
				$session->setNameTag($session->getCoreUser()->getRank()->getNameTagFormatFor($session));
				$session->setScoreTag("§r§f" . $session->getHealth() . TextFormat::RED . " ❤");
			}
		}
	}

	public function getAllData(callable $callback) : void {
		Database::get()->executeSelect("player.getAll", [], function(array $rows) use($callback) {
			$users = [];
			
			foreach($rows as [
				"xuid" => $xuid,
				"username" => $name,
				"money" => $money,
				"permissions" => $permissions
            ]) {
				$coreUser = new CoreUser($xuid);
				$users[$xuid] = $coreUser;
				
				$coreUser->setName($name);
				$coreUser->setMoney($money);
				$coreUser->setPermissions(unserialize($permissions));
			}
			$callback($users);
        });
	}
    /**
     * @return CoreUser[]
     */
    public function getAll() : array {
        return $this->coreUsers;
    }

    public function register(CorePlayer $player) : void {
		$name = $player->getName();
		$ip = $player->getNetworkSession()->getIp();

       	Database::get()->executeInsert("player.register", [
            "xuid" => $player->getXuid(),
            "registerDate" => date("m-d-Y g:iA"),
            "username" => $name,
            "ip" => $ip
        ]);
		$coreUser = new CoreUser($player->getPlayerInfo()->getXuid());
		$coreUser->name = $name;
		$coreUser->ip = $ip;
		$coreUser->rank = RankHandler::get("Member");
		$coreUser->setLoaded();
		$player->initialize($coreUser);
		$this->coreUsers[$player->getPlayerInfo()->getXuid()] = $coreUser;
    }
}