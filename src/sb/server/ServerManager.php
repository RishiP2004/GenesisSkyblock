<?php

declare(strict_types = 1);

namespace sb\server;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use sb\database\Database;
use sb\item\CustomItemParser;
use sb\player\CorePlayer;
use sb\server\fund\FundHandler;
use sb\server\shop\ShopHandler;
use sb\server\broadcast\BroadcastHandler;
use sb\server\jackpot\JackpotHandler;
use sb\server\warps\WarpHandler;
use sb\Skyblock;
use sb\utils\SimpleReward;

class ServerManager implements ServerData {
	use SingletonTrait;

	private int $voteGoal;

	private array $rewards = [];

	public function __construct() {
		new JackpotHandler();
		new WarpHandler(Skyblock::getInstance());
		new ShopHandler();
		new FundHandler(Skyblock::getInstance());

		Database::get()->executeSelect("server.get", [], function(array $rows) {
			if(count($rows) === 0) {
				$this->voteGoal = 0;
				return;
			}
			$data = $rows[0];
			$this->voteGoal = $data['voteGoal'];
		});
		foreach(self::REWARDS as $name => $data) {
			$items = [];
			$cmds = [];

			foreach($data["items"] as $item) {
				$items[] = CustomItemParser::getInstance()->parse($item);
			}
			foreach($data["cmds"] as $cmd) {
				$cmds[] = $cmd;
			}
			$this->initReward(new SimpleReward($name, $items, $cmds));
		}
	}

	public function tick() : void {
		BroadcastHandler::tick();
		JackpotHandler::tick();
	}

	public function getRewards() : array {
		return $this->rewards;
	}

	public function getReward(string $type) : SimpleReward {
		return $this->rewards[strtolower($type)];
	}

	public function initReward(SimpleReward $reward) : SimpleReward {
		return $this->rewards[strtolower($reward->getName())] = $reward;
	}

	public function getVoteGoal() : int {
		return $this->voteGoal;
	}

	public function setVoteGoal(int $goal) : void {
		$this->voteGoal = $goal;
		$this->save();
	}

	public function increaseVoteGoal(int $amount = 1): int {
		$this->setVoteGoal($this->voteGoal + $amount);

		return $this->voteGoal;
	}

	public function vote(CorePlayer $player) : void {
		$this->increaseVoteGoal();

		foreach($this->getReward(self::VOTE_REWARD)->getCmds() as $cmd) {
			Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), str_replace('{PLAYER}', $player->getName(), $cmd));
		}
		foreach($this->getReward(self::VOTE_REWARD)->getItems() as $item) {
			if($item instanceof Item) {
				$player->getInventory()->canAddItem($item) ? $player->getInventory()->addItem($item) : $player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
			}
		}
		Server::getInstance()->broadcastMessage(self::PREFIX . "§c{$player->getName()}§7 has voted and received a vote crate and $5,000"); //todo: change for later

		if($this->getVoteGoal() >= 100) {
			$this->setVoteGoal(0);
			Server::getInstance()->broadcastMessage(self::PREFIX . "§cVote Goal§7 has been reached, everyone online got a Elite Crate!");

			foreach(Server::getInstance()->getOnlinePlayers() as $player) {
				foreach($this->getReward(self::VOTE_GOAL_REWARD)->getItems() as $item) {
					$player->getInventory()->canAddItem($item) ? $player->getInventory()->addItem($item) : $player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
				}
			}
		}
	}

	public function save() : void {
		Database::get()->executeChange("server.update", [
			"voteGoal" => $this->getVoteGoal()
		]);
	}
}