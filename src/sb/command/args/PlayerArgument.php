<?php

namespace sb\command\args;

use CortexPE\Commando\args\BaseArgument;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class PlayerArgument extends BaseArgument {
    protected bool $exact;

    public function __construct(string $name, bool $optional = false, bool $exact = true) {
        parent::__construct($name, $optional);
        $this->exact = $exact;
    }

    public function getNetworkType() : int {
        return AvailableCommandsPacket::ARG_TYPE_TARGET;
    }

    public function canParse(string $testString, CommandSender $sender) : bool {
        return Player::isValidUserName($testString);
    }

    public function parse(string $argument, CommandSender $sender) :mixed {
        return Server::getInstance()->getPlayerByPrefix($argument);
    }

    public function getTypeName() : string {
        return 'player';
    }
}