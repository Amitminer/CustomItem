<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Manager;

use AmitxD\CustomItem\Manager\CustomItemsManager;
use pocketmine\player\Player;
use OmniLibs\libs\jojoe77777\FormAPI\SimpleForm;

class FormManager {

    public function __construct() {
        // NOOP (No operation)
    }

    /**
     * Display the custom items selection form to the player.
     * @param Player $player The player to whom the form will be displayed.
     */
    public static function displayCustomItemsForm(Player $player): void {
        $form = new SimpleForm(function(Player $player, $data) {
            if ($data === null) {
                return true;
            }
            switch ($data) {
                case 0: CustomItemsManager::getFrezzing($player);
                    break;
                case 1:
                    CustomItemsManager::getTeleportationSword($player);
                    break;
                case 2:
                    CustomItemsManager::getTimeController($player);
                    break;
                case 3:
                    break;
            }
        });

        $form->setTitle("§aCustomsItems!");
        $form->addButton("§bFrezzing Sword");
        $form->addButton("§cTELEPORTATION Sword");
        $form->addButton("§cTime-Controller");
        $form->addButton("TODO:");
        $player->sendForm($form);
    }
}
