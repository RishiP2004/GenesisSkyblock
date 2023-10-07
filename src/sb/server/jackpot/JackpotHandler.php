<?php

namespace sb\server\jackpot;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use sb\command\player\JackPotCommand;
use sb\database\Database;
use sb\player\PlayerManager;
use sb\player\PlayerSortableData;
use sb\player\traits\PlayerCallTrait;
use sb\server\ServerManager;
use pocketmine\Server;
use pocketmine\world\sound\XpLevelUpSound;
use sb\player\CorePlayer;
use sb\Skyblock;

class JackpotHandler {
	/**BRO THIS CODE IS SO ASS? ill have to rewrite this*/
	const PREFIX = "\n                 §r§b§lSaturn §r§8| §r§c§lJackpot \n§r";

	public static int $prizepool = 0;

	public static int $time = 30;

	public static array $players = [];

	public static int $default = 0;

	public function __construct() {
		self::setPrizePool(0);
	}

	public static function tick() : void {
		--self::$time;

		if (self::$time <= 0) {
			if (count(self::$players) <= 0) {
				Server::getInstance()->broadcastMessage(self::PREFIX . "Failed to draw a winner, could not find any ticket(s).\n");
				self::$time = rand(4000,10000);
			}
			if (count(self::$players) >= 1 && self::$time <= 0) {
				self::drawWinner();
				self::$time = rand(4000,10000);
			}
		}
	}

	public static function sendBuyForm(CorePlayer $player, int $amount, int $tickets) : void {
		$options = [
			new MenuOption("§r§2Confirm Purchase"),
			new MenuOption("§r§4Cancel Purchase"),
		];
		$m = "§r§6§l§r§5§lGenesis's Ticket Merchant \n §r§fYou are about to purchase §r§2$tickets §r§fticket(s) §r§for §r§2$$amount!";

		$player->sendForm(new MenuForm(
			"§r§8Confirm Ticket Purchase",
			$m,
			$options,
			function(Player $player, int $selectedOption) use($amount, $tickets) : void {
				if($selectedOption == 0) {
					self::addTickets($player,$tickets, $amount);
				}
			}
		));
	}

	public static function setPrizePool(int $prize) : void {
		self::$prizepool = $prize;
	}

	public static function getPrizePool() : int {
		return self::$prizepool;
	}

	public static function drawWinner() : void {
		$tickets = [];
		foreach (self::$players as $player => $amount) {
			for ($i = 0; $i < $amount; $i++) {
				$tickets[] = $player;
			}
		}
		shuffle($tickets);
		$player = $tickets[array_rand($tickets)];

		PlayerManager::getInstance()->getCoreUser($player, function($user) use ($tickets) {
			if(is_null($user)) {
				return false;
			} else {
				$user->addMoney(self::getPrizePool());
				$user->increaseJackpotWins();
				$user->increaseJackpotEarnings(self::getPrizePool());

				$pool = number_format(self::$prizepool, 2);
				$mytickets = self::getTickets($user->getName());
				$ticketz = number_format(count($tickets), 2);
				Server::getInstance()->broadcastMessage(self::PREFIX . "§r§a{$user->getName()} has won the /jackpot and received \n §r§2$" . $pool . "§r§a! \n §r§aThey purchased {$mytickets} ticket(s) \n §r§aout of the $ticketz ticket(s) sold!\n");
				self::setPrizePool(rand(100000, 8383838));
				self::$players = array();

				$player = Server::getInstance()->getPlayerByPrefix($user->getName());

				if($player instanceof CorePlayer) {
					$player->sendMessage(Skyblock::PREFIX . "You won the jackpot of §a$" . $this->getPrizePool() . "!");
				}
				return true;
			}
		});
	}

	public static function getTickets(string $player) : int {
		if(!isset(self::$players[$player])) return 0;
		return (int) self::$players[$player];
	}

	public static function addTickets(CorePlayer $player, int $amount = 1, int $price = 1) : void {
		$session = $player->getCoreUser();

		if($session->getMoney() < $price){
			$player->sendMessage(Skyblock::PREFIX . "You don't have the required balance to purchase tickets.");
			return;
		}
		$tickets = self::$players[$player->getName()] ?? 0;
		$session->reduceMoney($price);
		self::$prizepool += $price;
		$player->sendMessage(Skyblock::PREFIX . "You succesfully purchased x$amount Ticket(s).");
		$player->getWorld()->addSound($player->getLocation(),new XpLevelUpSound(1000));
		self::$players[$player->getName()] = $tickets + $amount;
	}
}

