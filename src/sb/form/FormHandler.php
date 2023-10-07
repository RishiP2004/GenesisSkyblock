<?php

namespace sb\form;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class FormHandler {

    /**
     * @param string $name
     * @param string $content
     * @param array<EasyFormButton> $buttons
     * @return MenuForm
     */
    public static function easyForm(string $name, string $content, array $buttons) : MenuForm {
        $goodButtons = array_values(array_filter(array_filter($buttons, fn(EasyFormButton $button) => $button->shouldBeShown())));
        return new MenuForm(
            $name,
            $content,
            [...array_map(fn(EasyFormButton $button) => $button->build(), $goodButtons), new MenuOption("Close Form")],
            function(Player $submitter, int $selected) use ($goodButtons) : void {
                if (is_null($chosen = $goodButtons[$selected] ?? null)) {
                    return;
                }
                $chosen->run($submitter);
            }
        );
    }

    /**
     * @param string $name
     * @param string $content
     * @param array<CommandFormButton> $buttons
     * @return MenuForm
     */
    public static function commandForm(string $name, string $content, array $buttons) : MenuForm {
        $goodButtons = array_values(array_filter(array_filter($buttons, fn(CommandFormButton $button) => $button->shouldBeShown())));
        return new MenuForm(
            $name,
            $content,
            [...array_map(fn(CommandFormButton $button) => $button->build(), $goodButtons), new MenuOption("Close Form")],
            function(Player $submitter, int $selected) use ($goodButtons) : void {
                if (is_null($chosen = $goodButtons[$selected] ?? null)) {
                    return;
                }
                $chosen->run($submitter);
            }
        );
    }

    public static function textInputForm(string $name, string $placeholder, string $failedValMsg, callable $validateText, callable $whenDone) : CustomForm {
        return new CustomForm(
            $name,
            [new Input("input", $placeholder)],
            function(Player $submitter, CustomFormResponse $response) use ($validateText, $whenDone, $failedValMsg) : void {
                $input = $response->getString("input");
                if (!$validateText($input)) {
                    $submitter->sendMessage($failedValMsg);
                    return;
                }
                $whenDone($input);
            }
        );
    }

}