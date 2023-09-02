<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Events;

use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\world\World;
use pocketmine\math\VoxelRayTrace;
use AmitxD\CustomItem\libs\TimerAPI\TimerAPI;
use AmitxD\CustomItem\CustomItem;

class TeleportationSword implements Listener {
    
    public function __construct() {
        // NOOP
    }
    
    public function onPlayerItemUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $CheckNamedTag = $item->getNamedTag()->getTag("TP-Sword");
        if ($CheckNamedTag !== null && $this->isPlayer($player)) {
            if(TimerAPI::hasCooldown($player,$item)){
                $timeRemaining = TimerAPI::getCooldownTimeRemaining($player,$item);
                $player->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §cThe TeleportationSword is on cooldown for {$timeRemaining} seconds.");
                $event->cancel();
                return;
            }
            $start = $player->getPosition()->add(0, $player->getEyeHeight(), 0);
			$end = $start->addVector($player->getDirectionVector()->multiply($player->getViewDistance() * 18));
			$world = $player->getWorld();
			foreach(VoxelRayTrace::betweenPoints($start, $end) as $vector3){
			    TimerAPI::startCooldown($player, 3,$item);
			    if($vector3->y >= World::Y_MAX or $vector3->y <= 0){
			        return;
				        }
				if(($result = $world->getBlock($vector3)->calculateIntercept($start, $end)) !== null){
				    $target = $result->hitVector;
				    $player->teleport($target);
				    return;
				        }
			      }
        }
    }
    private function isPlayer($player): bool {
        return $player instanceof Player;
    }
}