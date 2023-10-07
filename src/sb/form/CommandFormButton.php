<?php

namespace sb\form;

use dktapps\pmforms\FormIcon;
use pocketmine\player\Player;

class CommandFormButton extends EasyFormButton {

    public function __construct(string $text, ?FormIcon $icon, bool $condition, string $command) {
        parent::__construct($text, $icon, $condition, function(Player $player) use($command) : void {
            $player->chat("/" . $command);
        });
    }

}