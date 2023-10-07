<?php

declare(strict_types = 1);

namespace sb\command;

use sb\command\islands\IslandCommand;
use sb\command\player\BalanceCommand;
use sb\command\player\coinflip\CoinflipCommand;
use sb\command\player\CustomEnchantsCommand;
use sb\command\player\CustomItemCommand;
use sb\command\player\DeleteHomeCommand;
use sb\command\player\FarmingCommand;
use sb\command\player\fund\FundCommand;
use sb\command\player\HomeCommand;
use sb\command\player\JackPotCommand;
use sb\command\player\kit\KitCommand;
use sb\command\player\ListHomesCommand;
use sb\command\player\PayCommand;
use sb\command\player\PingCommand;
use sb\command\player\rank\RankCommand;
use sb\command\player\rank\RanksCommand;
use sb\command\player\rank\SetRankCommand;
use sb\command\player\RewardsCommand;
use sb\command\player\SetHomeCommand;
use sb\command\player\SetsCommand;
use sb\command\player\ShopCommand;
use sb\command\player\SlotBotCommand;
use sb\command\player\SpawnCommand;
use sb\command\player\staff\AccountsCommand;
use sb\command\player\staff\AddEssenceCommand;
use sb\command\player\staff\AddMoneyCommand;
use sb\command\player\staff\AddPlayerPermissionCommand;
use sb\command\player\staff\AddUluruCommand;
use sb\command\player\staff\DeleteAccountCommand;
use sb\command\player\staff\FlyCommand;
use sb\command\player\staff\ListPlayerPermissionsCommand;
use sb\command\player\staff\ReduceEssenceCommand;
use sb\command\player\staff\ReduceMobCoinsCommand;
use sb\command\player\staff\ReduceMoneyCommand;
use sb\command\player\staff\ReduceUluruCommand;
use sb\command\player\staff\RemovePlayerPermissionCommand;
use sb\command\player\staff\SetEssenceCommand;
use sb\command\player\staff\SetMobCoinsCommand;
use sb\command\player\staff\SetMoneyCommand;
use sb\command\player\staff\SetUluruCommand;
use sb\command\player\staff\StaffModeCommand;
use sb\command\player\staff\TicketCommand;
use sb\command\player\TopMoneyCommand;
use sb\command\player\TradeCommand;
use sb\command\player\WarpCommand;
use sb\command\world\islandCache\IslandCacheCommand;
use sb\command\world\koth\KothCommand;
use sb\Skyblock;

use pocketmine\event\EventPriority;
use pocketmine\event\server\CommandEvent;

use pocketmine\Server;

class CommandHandler {
	private array $skipList = [];
	private array $replaceMap = [];

	public function __construct() {
		self::registerPlayerCommands();
		self::registerGameCommands();

		Server::getInstance()->getPluginManager()->registerEvent(CommandEvent::class,
			function(CommandEvent $event) : void {
				$args = explode(" ", rtrim($event->getCommand(), "\r\n"));
				$label = array_shift($args);

				if(isset($this->skipList[$label])) {
					return;
				}
				if(isset($this->replaceMap[$label])) {
					$event->setCommand(implode(" ", [$this->replaceMap[$label], ...$args]));
					return;
				}
				$knownCommands = Server::getInstance()->getCommandMap()->getCommands();

				if(isset($knownCommands[$label])) {
					$this->skipList[$label] = true;
					return;
				}
				foreach($knownCommands as $key => $value){
					if(strcasecmp($label, $find = $key) === 0 or strcasecmp($label, $find = $value->getLabel()) === 0) {
						$this->replaceMap[$label] = $find;
						$event->setCommand(implode(" ", [$find, ...$args]));
						return;
					}
				}
				$this->skipList[$label] = true;
			}, EventPriority::LOW, Skyblock::getInstance());
	}

	private static function registerPlayerCommands() : void {
		Server::getInstance()->getCommandMap()->registerAll("player",
			[
				new FlyCommand(Skyblock::getInstance(), "fly"),
				new PingCommand(Skyblock::getInstance(), "ping"),
				new AccountsCommand(Skyblock::getInstance(), "accounts"),
				new AddEssenceCommand(Skyblock::getInstance(), "addessence"),
				new AddMoneyCommand(Skyblock::getInstance(), "addmoney"),
				new AddPlayerPermissionCommand(Skyblock::getInstance(), "addplayerpermission"),
				new AddUluruCommand(Skyblock::getInstance(), "adduluru"),
				new BalanceCommand(Skyblock::getInstance(), "balance"),
				new DeleteAccountCommand(Skyblock::getInstance(), "deleteaccount"),
				new ListPlayerPermissionsCommand(Skyblock::getInstance(), "listpperm"),
				new PayCommand(Skyblock::getInstance(), "pay", "Pay someone"),
				new ReduceEssenceCommand(Skyblock::getInstance(), "reduceessence"),
				new ReduceMobCoinsCommand(Skyblock::getInstance(), "reducemobcoins"),
				new ReduceMoneyCommand(Skyblock::getInstance(), "reducemoney"),
				new ReduceUluruCommand(Skyblock::getInstance(), "reduceuluru"),
				new RemovePlayerPermissionCommand(Skyblock::getInstance(), "removeplayerpermission"),
				new SetEssenceCommand(Skyblock::getInstance(), "setessence"),
				new SetMobCoinsCommand(Skyblock::getInstance(), "setmobcoins"),
				new SetMoneyCommand(Skyblock::getInstance(), "setmoney"),
				new SetUluruCommand(Skyblock::getInstance(), "setuluru"),
				new SlotBotCommand(Skyblock::getInstance(), "slotbot"),
				new StaffModeCommand(Skyblock::getInstance(), "staffmode"),
				new TicketCommand(Skyblock::getInstance(), "ticket"),
				new TopMoneyCommand(Skyblock::getInstance(), "topmoney"),
				new SetsCommand(Skyblock::getInstance(), "sets"),
				new DeleteHomeCommand(Skyblock::getInstance(), "deletehome"),
				new HomeCommand(Skyblock::getInstance(), "home"),
				new ListHomesCommand(Skyblock::getInstance(), "listhomes"),
				new SetHomeCommand(Skyblock::getInstance(), "sethome"),
				new RewardsCommand(Skyblock::getInstance(), "rewards"),
				new TradeCommand(Skyblock::getInstance(), "trade"),
				new SpawnCommand(Skyblock::getInstance(), "spawn"),
				new CustomItemCommand(Skyblock::getInstance(), "customitem"),
				new WarpCommand(Skyblock::getInstance(), "warp"),
				new KothCommand(Skyblock::getInstance(), "koth"),
				new IslandCacheCommand(Skyblock::getInstance(), "islandcache"),
				new ShopCommand(Skyblock::getInstance(), "shop"),
				new JackPotCommand(Skyblock::getInstance(), "jackpot"),
				new RankCommand(Skyblock::getInstance(), "rank"),
				new RanksCommand(Skyblock::getInstance(), "ranks"),
				new SetRankCommand(Skyblock::getInstance(), "setrank"),
				new CoinflipCommand(Skyblock::getInstance(), "coinflip"),
				new FarmingCommand(Skyblock::getInstance(), "farming"),
				new CustomEnchantsCommand(Skyblock::getInstance(), "customenchants"),
				new KitCommand(Skyblock::getInstance(), "kit"),
				new FundCommand(Skyblock::getInstance(), "fund")
			]
		);
	}

	private static function registerGameCommands() : void {
		Server::getInstance()->getCommandMap()->registerAll("game",
			[
				new IslandCommand()
			]
		);
	}
}