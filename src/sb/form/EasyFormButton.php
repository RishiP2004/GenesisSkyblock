<?php

namespace sb\form;

use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class EasyFormButton {

    private string $text;

    private ?FormIcon $icon;

    private bool $condition;

    private $onPress;

    public function __construct(string $text, ?FormIcon $icon, bool $condition, callable $onPress) {
        $this->text = $text;
        $this->icon = $icon;
        $this->condition = $condition;
        $this->onPress = $onPress;
    }

    public function build() : MenuOption {
        return new MenuOption($this->text, $this->icon);
    }

    public function shouldBeShown() : bool {
        return $this->condition;
    }

    public function run(Player $player) : void {
        ($this->onPress)($player);
    }

}