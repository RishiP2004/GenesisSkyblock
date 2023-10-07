<?php

declare(strict_types = 1);

namespace sb;

use pocketmine\lang\Language;
use pocketmine\utils\SingletonTrait;
use ReflectionClass;
use sb\command\CommandHandler;
use sb\database\Database;
use sb\database\IslandDB;
use sb\entity\EntityManager;
use sb\event\ListenerRegistry;
use sb\islands\IslandManager;
use sb\block\BlockManager;
use sb\item\enchantment\CustomEnchantments;
use sb\item\CustomItems;
use sb\lang\CustomKnownTranslationFactory;
use sb\permission\CorePermissionManager;
use sb\player\PlayerManager;
use sb\server\fund\FundHandler;
use sb\server\ServerData;
use sb\server\ServerManager;
use sb\utils\ResourceInitializer;
use sb\world\WorldManager;
use sb\scheduler\CoreScheduler;

use muqsit\invmenu\InvMenuHandler;

use CortexPE\Commando\PacketHooker;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\TextFormat;
use Symfony\Component\Filesystem\Path;

//todo: clean up function names and manager stuff. and overall configs
//change scoreboard depending on where.
//clean up menus and forms
//cache system trait?
//listeners
//weird code
//translation syst
//implement constants for bases.
//rewrite later.
class Skyblock extends PluginBase {
    private CoreScheduler $coreTask;

	use SingletonTrait;

    const PREFIX = "§l§bSaturn §r§l§f»§r§7 ";
    const ERROR_PREFIX = TextFormat::DARK_RED;

    public function onLoad() : void {
        self::$instance = $this;
    }

    public function onEnable() : void {
		$this->getServer()->getNetwork()->setName(ServerData::SERVER_NAME);

		if(!PacketHooker::isRegistered()) {
			PacketHooker::register($this);
		}
		if(!InvMenuHandler::isRegistered()) {
			InvMenuHandler::register($this);
		}
		$this->initManagers();

        $this->getScheduler()->scheduleRepeatingTask(new CoreScheduler(), 20);

        $this->getServer()->getLogger()->notice(self::PREFIX . "Skyblock Enabled");
    }

	public function initManagers() {
		ResourceInitializer::initialize();
		Database::initialize();
		IslandDB::initialize();

		CustomItems::getAll();
		CustomItems::initHack();

		CorePermissionManager::setup();
		new BlockManager();
		new WorldManager();
		new EntityManager();
		new IslandManager();
		new PlayerManager();
		new ServerManager();
		new CommandHandler();

		//HOT FIX, change when Enchants are actually called.
		CustomEnchantments::getAll();
		ListenerRegistry::register();
	}

    public function onDisable() : void {
		FundHandler::getInstance()->saveAll();
		Database::get()->waitAll();
		Database::get()->close();
		//kick users?
		$this->getScheduler()->cancelAllTasks();
        $this->getServer()->getLogger()->notice(self::PREFIX. "Skyblock Disabled");
    }
}
