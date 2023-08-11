<?php

declare(strict_types=1);

namespace AmitxD\CustomItem\libs\TimerAPI;

use pocketmine\scheduler\ClosureTask;
use pocketmine\command\ConsoleCommandSender;
use AmitxD\CustomItem\CustomItem;
use pocketmine\Server;
use pocketmine\player\Player;

class TimerAPI {
    
    private static $cooldowns = [];
    
    /**
     * Schedules a task to be executed after a specified duration.
     *
     * @param callable $callback The callback function to execute.
     * @param int      $duration The duration in seconds.
     */
    public static function wait(callable $callback, int $duration): void {
        CustomItem::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask($callback), $duration * 20);
    }
    /**
     * Starts a cooldown for the specified player.
     *
     * @param Player $player   The player to start the cooldown for.
     * @param int    $duration The duration of the cooldown in seconds.
     */
    public static function startCooldown(Player $player, int $duration): void {
        self::$cooldowns[$player->getName()] = time() + $duration;
    }

    /**
     * Checks if the player has an active cooldown.
     *
     * @param Player $player The player to check.
     * @return bool Whether the player has an active cooldown.
     */
    public static function hasCooldown(Player $player): bool {
        return isset(self::$cooldowns[$player->getName()]) && time() < self::$cooldowns[$player->getName()];
    }

    /**
     * Gets the remaining time for the player's cooldown.
     *
     * @param Player $player The player to check.
     * @return int The remaining time in seconds.
     */
    public static function getCooldownTimeRemaining(Player $player): int {
        $timeRemaining = self::$cooldowns[$player->getName()] - time();
        return max(0, $timeRemaining);
    }
}
