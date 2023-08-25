<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Events;

use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\player\Player;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\server\CommandEvent;
use AmitxD\CustomItem\libs\TimerAPI\TimerAPI;
use AmitxD\CustomItem\CustomItem;

class TimeController implements Listener {

    private $frozenPlayers = [];

    public function __construct(private CustomItem $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerItemUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $CheckNamedTag = $item->getNamedTag()->getTag("TimeController");
        if ($item->getTypeId() === ItemTypeIds::COMPASS && $CheckNamedTag !== null && $this->isPlayer($player)) {
            //  echo "used";
            if (TimerAPI::hasCooldown($player, $item)) {
                $timeRemaining = TimerAPI::getCooldownTimeRemaining($player, $item);
                $player->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §cThe TimeController is on cooldown for {$timeRemaining} seconds.");
                $event->cancel();
                return;
            }
            $players = $player->getWorld()->getPlayers();
            foreach ($players as $target) {
                if ($target !== $player) {
                    $this->freezePlayer($target, 30, $player);
                    TimerAPI::startCooldown($player, 30, $item);
                    $player->getWorld()->stopTime();
                    $player->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §bTime has been stopped for 30 seconds!");
                }
            }
        }
    }

    private function isPlayer($entity): bool {
        return $entity instanceof Player;
    }

    public function onCmdRun(CommandEvent $event) {
        $sender = $event->getSender();
        if (isset($this->frozenPlayers[$sender->getName()]) && $this->frozenPlayers[$sender->getName()] === true) {
            $event->cancel();
        }
    }

    private function freezePlayer(Player $player, int $duration, Player $user): void {
        $this->frozenPlayers[$player->getName()] = true;
        $player->setMotion(new Vector3(0, 0, 0));
        $player->setNoClientPredictions(true);
        $player->sendMessage("§cYoure frezzz now for 30sec!");
        TimerAPI::wait(function() use ($player,$user) {
            $this->unfreezePlayer($player,$user);
        }, $duration);
    }

    private function unfreezePlayer(Player $player, Player $user): void {
        unset($this->frozenPlayers[$player->getName()]);
        if ($user->isOnline()) {
            $user->sendMessage("§aResumed the time!");
        }
        $user->getWorld()->startTime();
        $player->setNoClientPredictions(false);
    }
}