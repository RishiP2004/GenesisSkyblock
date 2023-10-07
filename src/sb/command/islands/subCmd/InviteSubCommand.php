<?php

namespace sb\command\islands\subCmd;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use sb\islands\traits\IslandCallTrait;
use sb\permission\utils\IslandPermissions;
use sb\player\CorePlayer;
use sb\Skyblock;

class InviteSubCommand extends BaseSubCommand {
	use IslandCallTrait;

	public function __construct() {
		parent::__construct(Skyblock::getInstance(), "invite", "Invite a player to your island.");
		$this->setPermission("pocketmine.command.me");
	}

	public function prepare() : void {
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	/** @var Player $sender */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
		$user = $sender->getCoreUser();

		if (!$user->hasIsland()) {
			$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have an island.");
			return;
		}

		$this->getIsland($user->getIsland(), function($island) use ($sender, $user) {
			if(is_null($island)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have an island");
				return false;
			}
			if(!$island->getRole($user)->hasPermission(IslandPermissions::PERMISSION_INVITE)) {
				$sender->sendMessage(Skyblock::ERROR_PREFIX . "You do not have permission to do this.");
				return false;
			}
			$players = [];
			/**
			 * @var CorePlayer $player
			 */
			foreach(Server::getInstance()->getOnlinePlayers() as $player) {
				if($player->getIslandName() == null and !$player->hasIslandInvite($island->getName())) {
					$players[$player->getName()] = $player;
				}
			}
			$sender->sendForm(new CustomForm("Invite Player", [new Input("player", "Pick a player")], function(Player $submitter, CustomFormResponse $response) use ($players, $island, $player) : void {
				if($island->getUpgrade("Member Size")->getMaxMembers() <= count($island->getMembers())) {
					$submitter->sendMessage(Skyblock::ERROR_PREFIX . "Your island has the max members.");
					return;
				}
				$invited = $response->getString("player");
				var_dump($invited);
				$p = Server::getInstance()->getPlayerByPrefix($invited);

				if(!$p instanceof CorePlayer) {
					$submitter->sendMessage(Skyblock::ERROR_PREFIX . "This player is no longer online.");
					return;
				}
				if($p->getIslandName() !== "") {
					$submitter->sendMessage(Skyblock::ERROR_PREFIX . "This player is already in an island.");
					return;
				}
				if($p->hasIslandInvite($island->getName())) {
					$submitter->sendMessage(Skyblock::ERROR_PREFIX . "This player has already been invited to your island");
					return;
				}
				$p->addIslandInvite($island->getName());
				$island->announce(Skyblock::PREFIX . "§c{$player->getName()}§7 has invited §c{$p->getName()}§7 to the island.");
				$p->sendMessage(Skyblock::PREFIX . "You have been invited to §c{$island->getName()} §7by §c{$player->getName()}.");
			}));
			return true;
		});
	}
}