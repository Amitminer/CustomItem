<?php

declare(strict_types=1);

namespace AmitxD\CustomItem\Items;

use customiesdevs\customies\item\CustomiesItemFactory;

class CustomItems {

    private const ITEM_CLASSES = [
        FreezingSword::class,
        TeleportationSword::class,
        LightningSword::class
    ];

    private const ITEM_NAMES = [
        'FreezingSword',
        'TeleportationSword',
        'LightningSword'
    ];

    private const ITEM_IDS = [
        'customitem:freezing_sword',
        'customitem:teleportation_sword',
        'customitem:lightning_sword'
    ];

    /**
     * Load custom items into the game.
     */
    public static function loadItems(): void {
        foreach (self::ITEM_CLASSES as $index => $class) {
            self::registerItem($class, self::ITEM_IDS[$index], self::ITEM_NAMES[$index]);
        }
    }

    /**
     * Register a custom item using the CustomiesItemFactory.
     *
     * @param string $class The class of the custom item.
     * @param string $itemId The ID of the custom item.
     * @param string $itemName The name of the custom item.
     */
    private static function registerItem(string $class, string $itemId, string $itemName): void {
        CustomiesItemFactory::getInstance()->registerItem($class, $itemId, $itemName);
    }
}