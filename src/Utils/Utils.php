<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Utils;

use pocketmine\player\Player;
use pocketmine\item\Item;
use pocketmine\entity\Entity;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;
use AmitxD\CustomItem\Utils\EnchantmentIds;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\NetworkBroadcastUtils;

class Utils {

    public const FAKE_ENCH_ID = -1;

    /**
    * Converts a string representation of an item to an Item instance.
    * @param string $input The string representation of the item.
    * @return Item|null The Item instance if the conversion was successful, or null if failed.
    */
    public static function stringToItem(string $input): ?Item {
        $string = strtolower(str_replace([' ', 'minecraft:'], ['_', ''], trim($input)));
        try {
            $item = StringToItemParser::getInstance()->parse($string) ?? LegacyStringToItemParser::getInstance()->parse($string);
        } catch (LegacyStringToItemParserException $e) {
            return null;
        }
        return $item;
    }

    /**
    * Converts a string representation of an enchantment to an Enchantment instance.
    * @param string $name The name of the enchantment.
    * @return Enchantment|null The Enchantment instance if the conversion was successful, or null if failed.
    */
    public static function stringToEnchantment(string $name) {
        $enchantment = StringToEnchantmentParser::getInstance()->parse($name);
        return $enchantment ?? null;
    }

    public static function createEnchantment(string $name, int $id): void {
        if ($name !== '') {
            EnchantmentIdMap::getInstance()->register($id, new Enchantment(strtolower($name), 1, ItemFlags::ALL, ItemFlags::NONE, 1));
        }
    }
    
    public static function summonLightning(Player $player) :void {
        $pos = $player->getPosition();
        $light = new AddActorPacket();
        $light->actorUniqueId = Entity::nextRuntimeId();
        $light->actorRuntimeId = 1;
        $light->position = $player->getPosition()->asVector3();
        $light->type = "minecraft:lightning_bolt";
        $light->yaw = $player->getLocation()->getYaw();
        $light->syncedProperties = new PropertySyncData([], []);
        $sound = PlaySoundPacket::create("ambient.weather.thunder", $pos->getX(), $pos->getY(), $pos->getZ(), 1, 1);
        NetworkBroadcastUtils::broadcastPackets($player->getWorld()->getPlayers(), [$light, $sound]);
    }
    
}