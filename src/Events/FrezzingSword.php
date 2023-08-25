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
use AmitxD\CustomItem\libs\TimerAPI\TimerAPI;
use pocketmine\player\Player;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\world\World;
use pocketmine\scheduler\ClosureTask;
use AmitxD\CustomItem\CustomItem;

class FrezzingSword implements Listener {

    private $frozenPlayers = [];

    public function __construct(private CustomItem $plugin) {
        $this->plugin = $plugin;
    }
 
    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void {
        $damager = $event->getDamager();
        $entity = $event->getEntity();
        if (!$damager instanceof Player || !$entity instanceof Player) {
            return;
        }
        $item = $damager->getInventory()->getItemInHand();
        $itemid = $item->getTypeId();
        $playerName = $entity->getName();
        // var_dump($itemid);
        $itemName = $item->getVanillaName();
        $itemname2 = $item->getName();
        $CheckNamedTag = $item->getNamedTag()->getTag("FrezzingSword");

        if ($item->getTypeId() === ItemTypeIds::DIAMOND_SWORD && $CheckNamedTag !== null) {
            if (TimerAPI::hasCooldown($damager, $item)){
                $timeRemaining = TimerAPI::getCooldownTimeRemaining($damager, $item);
                $damager->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §cThe Freezing Sword is on cooldown for {$timeRemaining} seconds.");
                $event->cancel();
                return;
            }
            $this->freezePlayer($entity, 5);
            TimerAPI::startCooldown($damager, 15, $item);
            $damager->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §b{$playerName} has been frozen for 5 seconds!");
        }
    }

    private function freezePlayer(Player $player, int $duration): void {
        $this->frozenPlayers[$player->getName()] = true;
        $player->setMotion(new Vector3(0, 0, 0));
        $player->setNoClientPredictions(true);
        TimerAPI::wait(function() use ($player) {
            $this->unfreezePlayer($player);
        }, $duration);
    }

    private function unfreezePlayer(Player $player): void {
        unset($this->frozenPlayers[$player->getName()]);
        $player->setNoClientPredictions(false);
    }
}