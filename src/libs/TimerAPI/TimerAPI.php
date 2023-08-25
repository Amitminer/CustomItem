<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\libs\TimerAPI;

use pocketmine\scheduler\ClosureTask;
use pocketmine\command\ConsoleCommandSender;
use AmitxD\CustomItem\CustomItem;
use pocketmine\Server;
use pocketmine\item\Item;
use pocketmine\player\Player;
use Closure;

class TimerAPI {

    private static $cooldowns = [];

    /**
    * Schedules a task to be executed after a specified duration.
    *
    * @param Closure $callback The callback function to execute.
    * @param int      $duration The duration in seconds.
    */
    public static function wait(Closure $callback, int $duration): void {
        CustomItem::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask($callback), $duration * 20);
    }

    /**
    * Starts a cooldown for the specified player for a given item.
    *
    * @param Player $player The player for whom the cooldown is started.
    * @param int $duration The duration of the cooldown in seconds.
    * @param Item $item The item associated with the cooldown.
    */
    public static function startCooldown(Player $player, int $duration, Item $item): void
    {
        if ($item->hasCustomName()) {
            $itemName = $item->getCustomName();
        } else {
            $itemName = $item->getName();
        }
        $itemTypeId = $item->getTypeId();
        $playerName = $player->getName();

        if (!isset(Self::$cooldowns[$playerName])) {
            Self::$cooldowns[$playerName] = [];
        }

        Self::$cooldowns[$player->getName()][$itemName] = [
            'time' => time() + $duration,
            'itemTypeId' => $itemTypeId,
        ];
    }

    /**
    * Checks if the player has an active cooldown for a specific item.
    *
    * @param Player $player The player to check the cooldown for.
    * @param Item $item The item to check the cooldown for.
    * @return bool Whether the player has an active cooldown for the item.
    */
    public static function hasCooldown(Player $player, Item $item): bool
    {
        // Check if the player's data exists and if the item is on cooldown
        if ($item->hasCustomName()) {
            $itemName = $item->getCustomName();
        } else {
            $itemName = $item->getName();
        }
        $itemTypeId = $item->getTypeId();
        if (isset(Self::$cooldowns[$player->getName()][$itemName])) {
            $cooldownData = Self::$cooldowns[$player->getName()][$itemName];

            if (time() < $cooldownData['time'] && $itemTypeId === $cooldownData['itemTypeId']) {
                return true;
            }
        }

        return false;
    }

    /**
    * Gets the remaining time in seconds for the player's cooldown for a specific item.
    *
    * @param Player $player The player for whom to retrieve the cooldown time.
    * @param Item $item The item associated with the cooldown.
    * @return int The remaining time in seconds for the cooldown.
    */
    public static function getCooldownTimeRemaining(Player $player, Item $item): int
    {
        // Check if the player's data exists and if the item is on cooldown
        if ($item->hasCustomName()) {
            $itemName = $item->getCustomName();
        } else {
            $itemName = $item->getName();
        }
        if (isset(Self::$cooldowns[$player->getName()][$itemName])) {
            $timeRemaining = Self::$cooldowns[$player->getName()][$itemName]['time'] - time();
            return intval(max(0, $timeRemaining));
        }

        return 0;
    }
}