<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Manager;

use AmitxD\CustomItem\Utils\Utils;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\data\bedrock\EnchantmentIdMap;
use AmitxD\CustomItem\Utils\EnchantmentsIds;
use pocketmine\item\Item;

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
    * Add an enchantment to the item.
    * @param string $enchantment The name of the enchantment to add.
    * @param int $level The level of the enchantment (default: 1).
    * @return ItemManager
    */
    public function setEnchantment(mixed $enchantment, int $level = 1): self {
        if (is_string($enchantment)) {
            $enchant = Utils::stringToEnchantment($enchantment);
        } elseif (is_int($enchantment)) {
            $enchant = EnchantmentIdMap::getInstance()->fromId($enchantment);
        } else {
            throw new \InvalidArgumentException("An error occurred while adding enchantment on item.");
        }

        if ($enchant !== null) {
            $enchantmentInstance = new EnchantmentInstance($enchant, $level);
            $this->item->addEnchantment($enchantmentInstance);
        }

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