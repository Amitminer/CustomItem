<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Manager;

use AmitxD\CustomItem\Utils\Utils;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\data\bedrock\EnchantmentIdMap;
use AmitxD\CustomItem\Utils\EnchantmentsIds;
use pocketmine\item\Item;
use InvalidArgumentException;

class ItemManager {

    private $item;

    /**
    * ItemManager constructor.
    * @param Item $item The base item instance to manage.
    */
    private function __construct(Item $item) {
        $this->item = $item;
    }

    /**
    * Get a custom item manager instance for the specified item name.
    * @param string $item The name of the custom item.
    * @return ItemManager
    */
    public static function getCustomItem(string $item): self {
        $itemInstance = Utils::stringToItem($item);
        return new self($itemInstance);
    }

    /**
    * Set the custom name for the item.
    * @param string $name The custom name to set.
    * @return ItemManager
    */
    public function setName(string $name): self {
        $this->item->setCustomName($name);
        return $this;
    }

    /**
    * Set the lore for the item.
    * @param array $lore An array of strings representing the item's lore.
    * @return ItemManager
    */
    public function setLore(array $lore): self {
        $this->item->setLore($lore);
        return $this;
    }

    /**
    * Adds an enchantment to the item.
    *
    * @param string|int $enchantment The name or ID of the enchantment to add.
    * @param int $level The level of the enchantment (default: 1).
    *
    * @throws InvalidArgumentException If the enchantment cannot be added.
    *
    * @return ItemManager
    */
    public function setEnchantment(mixed $enchantment, int $level = 1): self {
        if (is_numeric($enchantment)) {
            $enchant = EnchantmentIdMap::getInstance()->fromId($enchantment);
        } else {
            $enchant = Utils::stringToEnchantment($enchantment);
        }

        if ($enchant === null) {
            throw new InvalidArgumentException("An error occurred while adding an enchantment to the item.");
        }

        $enchantmentInstance = new EnchantmentInstance($enchant, $level);
        $this->item->addEnchantment($enchantmentInstance);

        return $this;
    }

    /**
    * Set a custom tag for the item.
    * @param string $nameTag The name of the custom tag.
    * @return ItemManager
    */
    public function setTag(string $nameTag): self {
        $this->item->getNamedTag()->setString($nameTag, $nameTag);
        return $this;
    }

    /**
    * Get the final customized item after all modifications.
    * @return Item The customized item instance.
    */
    public function getItem(): Item {
        return $this->item;
    }
}