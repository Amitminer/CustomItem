<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Manager;

use AmitxD\CustomItem\Utils\Utils;
use AmitxD\CustomItem\Manager\ItemManager;
use AmitxD\CustomItem\Utils\EnchantmentIds;
use AmitxD\CustomItem\CustomItem;

class CustomItemsManager {

    public function __construct() {
        // NOOP (No operation)
    }

    /**
    * Get the Frezzing Sword custom item and give it to the player.
    * @param mixed $player The player to whom the item will be given.
    */
    public static function getFrezzing($player): void {
        $item = ItemManager::getCustomItem("diamond_sword");
        $item->setName("§r§b§lFREEZING SWORD");
        $item->setLore(["§aFreezz Players for 5 Seconds!", "\n§cCooldown: 15 Seconds\n§r§l§9RARE"]);;
        $item->setEnchantment(EnchantmentIds::FREEZING, 1);
        //var_dump($ec);
        $item->setTag("FreezingSword");
        $player->getInventory()->addItem($item->getItem());
    }

    /**
    * Get the Teleportation Sword custom item and give it to the player.
    * @param mixed $player The player to whom the item will be given.
    */
    public static function getTeleportationSword($player): void {
        $item = ItemManager::getCustomItem("netherite_sword");
        $item->setName("§r§eTeleportation-Sword");
        $item->setLore(["§r§bRight Click To Teleport.\n\n§r§9RARE"]);
        $item->setEnchantment(EnchantmentIds::TELEPORTATION, 1);
        $item->setTag("TP-Sword");
        $player->getInventory()->addItem($item->getItem());
    }

    /**
    * Get the Time Controller custom item and give it to the player.
    * @param mixed $player The player to whom the item will be given.
    */

    public static function getTimeController($player): void {
        $item = ItemManager::getCustomItem("compass");
        $item->setName("§r§gTime Controller");
        $item->setLore(["§r§bRight Click To Change Time of Your World!.\n\n§r§9RARE"]);
        $item->setEnchantment(EnchantmentIds::TIMESTOPPER, 1);
        $item->setTag("TimeController");
        $player->getInventory()->addItem($item->getItem());
    }

    public static function getLightning($player): void {
        $item = ItemManager::getCustomItem("gold_sword");
        $item->setName("§r§b§lLIGHTNING-SWORD");
        $item->setLore([
            "§eUnleash lightning on foes!",
            "§7Damage over time on hit.",
            "\n§cCooldown: 15s\n\n§r§9RARE"
        ]);
        $item->setEnchantment(EnchantmentIds::LIGHTNING, 1);
        //var_dump($ec);
        $item->setTag("LightningSword");
        $player->getInventory()->addItem($item->getItem());
    }
}