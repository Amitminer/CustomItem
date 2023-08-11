<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Events;

use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\Server;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use AmitxD\CustomItem\libs\TimerAPI\TimerAPI;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\world\World;
use pocketmine\scheduler\ClosureTask;
use AmitxD\CustomItem\CustomItem;

class WeaponsEvents implements Listener {

    private $frozenPlayers = [];

    public function __construct(CustomItem $plugin) {
        $this->plugin = $plugin;
    }

    public function onEntityDamage(EntityDamageByEntityEvent $event): void {
        $damager = $event->getDamager();
        if (!$this->isPlayer($damager)) {
            return;
        }

        $entity = $event->getEntity();
        if (!$this->isPlayer($entity)) {
            return;
        }

        $item = $damager->getInventory()->getItemInHand();

        if ($this->isFreezingSword($item)) {
            $this->handleFreezingSword($damager, $entity, $item);
        } elseif ($this->isLightningSword($item)) {
            $this->handleLightningSword($damager, $entity, $item);
        }
    }

    private function handleFreezingSword(Player $damager, Player $entity, Item $item): void {
        if (TimerAPI::hasCooldown($damager)) {
            $timeRemaining = TimerAPI::getCooldownTimeRemaining($damager);
            $damager->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §cThe Freezing Sword is on cooldown for {$timeRemaining} seconds.");
            return;
        }

        $playerName = $entity->getName();
        $this->freezePlayer($entity, 5);
        TimerAPI::startCooldown($damager, 15);
        $damager->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §b{$playerName} has been frozen for 5 seconds!");
    }

    private function handleLightningSword(Player $damager, Player $entity, Item $item): void {
        if (TimerAPI::hasCooldown($damager)) {
            $timeRemaining = TimerAPI::getCooldownTimeRemaining($damager);
            $damager->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §cThe Lightning Sword is on cooldown for {$timeRemaining} seconds.");
            return;
        }

        $this->summonLightning($entity);
        TimerAPI::startCooldown($damager, 15);
    }

    public function isFreezingSword(Item $item): bool {
        $FreezingNameTag = $item->getNamedTag()->getTag("FreezingSword");
        if ($item->getTypeId() === ItemTypeIds::DIAMOND_SWORD && $FreezingNameTag) {
            return true;
        }
        return false;
    }

    public function isLightningSword(Item $item): bool {
        $LightningNameTag = $item->getNamedTag()->getTag("LightningSword");
        if ($item->getTypeId() === ItemTypeIds::GOLDEN_SWORD && $LightningNameTag) {
            return true;
        }
        return false;
    }

    private function isPlayer($entity): bool {
        return $entity instanceof Player;
    }

    public function summonLightning(Player $player) :void{
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