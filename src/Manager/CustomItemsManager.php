<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Manager;

use AmitxD\CustomItem\Utils\Utils;
use AmitxD\CustomItem\Manager\ItemManager;

class CustomItemsManager {
    
    public function __construct() {
        // NOOP (No operation)
    }
    
    /**
     * Get the Frezzing Sword custom item and give it to the player.
     * @param mixed $player The player to whom the item will be given.
     */
    public static function giveFrezzingSword($player): void {
        $item = ItemManager::getCustomItem("diamond_sword");
        $item->setName("§r§b§lFREZZING SWORD");
        $item->setLore(["§aFrezz Players for 5 Seconds!", "\n§cCooldown: 15 Seconds\n§r§l§9RARE"]);
        $item->setEnchantment("unbreaking", 3);
        $item->setTag("FrezzingSword");
        $player->getInventory()->addItem($item->getItem());
    }

    /**
     * Get the Teleportation Sword custom item and give it to the player.
     * @param mixed $player The player to whom the item will be given.
     */
    public static function giveTeleportationSword($player): void {
        $item = ItemManager::getCustomItem("netherite_sword");
        $item->setName("§r§eTeleportation-Sword");
        $item->setLore(["§r§bRight Click To Teleport.\n\n§r§9RARE"]);
        $item->setEnchantment("unbreaking", 3);
        $item->setTag("TP-Sword");
        $player->getInventory()->addItem($item->getItem());
    }

    /**
     * Get the Time Controller custom item and give it to the player.
     * @param mixed $player The player to whom the item will be given.
     */
    public static function giveTimeController($player): void {
        $item = ItemManager::getCustomItem("compass");
        $item->setName("§r§gTime Controller");
        $item->setLore(["§r§bRight Click To Change Time of Your World!.\n\n§r§9RARE"]);
        $item->setEnchantment("unbreaking", 3);
        $item->setTag("TimeController");
        $player->getInventory()->addItem($item->getItem());
    }
}
