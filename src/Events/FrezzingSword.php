<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Events;

use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\world\World;
use pocketmine\scheduler\ClosureTask;
use AmitxD\CustomItem\CustomItem;

class FrezzingSword implements Listener {

    private $frozenPlayers = [];

    public function __construct(CustomItem $plugin) {
        $this->plugin = $plugin;
    }
 
    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void {
        $damager = $event->getDamager();
        if (!$damager instanceof Player) {
            return;
        }
        $item = $damager->getInventory()->getItemInHand();
        $itemid = $item->getTypeId();
        // var_dump($itemid);
        $itemName = $item->getVanillaName();
        $itemname2 = $item->getName();
        $CheckNamedTag = $item->getNamedTag()->getTag("FrezzingSword");
        $entity = $event->getEntity();

        if ($item->getTypeId() === ItemTypeIds::DIAMOND_SWORD && $CheckNamedTag && $this->isPlayer($damager) && $this->isPlayer($entity)) {
            if ($this->hasCooldown($damager)) {
                $timeRemaining = $this->getCooldownTimeRemaining($damager);
                $damager->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §cThe Freezing Sword is on cooldown for {$timeRemaining} seconds.");
                $event->cancel();
                return;
            }
            $playerName = $entity->getName();
            $this->freezePlayer($entity, 5);
            $this->startCooldown($damager, 15);
            $damager->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §b{$playerName} has been frozen for 5 seconds!");
        }
    }
    private function hasCooldown(Player $player): bool {
        return isset($this->cooldowns[$player->getName()]) && time() < $this->cooldowns[$player->getName()];
    }
    private function getCooldownTimeRemaining(Player $player): int {
        $timeRemaining = $this->cooldowns[$player->getName()] - time();
        return max(0, $timeRemaining);
    }

    private function startCooldown(Player $player, int $duration): void {
        $this->cooldowns[$player->getName()] = time() + $duration;
    }
    private function isPlayer($entity): bool {
        return $entity instanceof Player;
    }

    private function freezePlayer(Player $player, int $duration): void {
        $this->frozenPlayers[$player->getName()] = true;
        $player->setMotion(new Vector3(0, 0, 0));
        $player->setNoClientPredictions(true);
        $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player) {
            $this->unfreezePlayer($player);
        }), $duration * 20);
    }

    private function unfreezePlayer(Player $player): void {
        unset($this->frozenPlayers[$player->getName()]);
        $player->setNoClientPredictions(false);
    }
}