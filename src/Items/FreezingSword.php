<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Items;

use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use customiesdevs\customies\item\CreativeInventoryInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent as EntityDamage;
use AmitxD\CustomItem\libs\TimerAPI\TimerAPI;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\TieredTool;
use pocketmine\item\ToolTier;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\player\Player;

class FreezingSword extends TieredTool implements ItemComponents {
    use ItemComponentsTrait;


    private $frozenPlayers = [];

    public function __construct(ItemIdentifier $identifier, string $name = "FreezingSword") {
        parent::__construct($identifier, $name, ToolTier::DIAMOND());
        $creativeInv = new CreativeInventoryInfo(CreativeInventoryInfo::GROUP_SWORD);
        $this->initComponent("FreezingSword", $creativeInv);
    }

    public function getMiningEfficiency(bool $isCorrectTool) : float {
        return parent::getMiningEfficiency($isCorrectTool) * 3.5;
    }

    public function onDestroyBlock(Block $block, array &$returnedItems) : bool {
        if (!$block->getBreakInfo()->breaksInstantly()) {
            return $this->applyDamage(2);
        }
        return false;
    }

    public function onAttackEntity(Entity $victim, array &$returnedItems) : bool {
        if (!$this->isPlayer($victim)) {
            return false;
        }
        $lastDamageCause = $victim->getLastDamageCause();
        if ($lastDamageCause instanceof EntityDamage) {
            $damager = $lastDamageCause->getDamager();
            if ($this->isPlayer($damager)) {
                // $item = $damager->$damager->getInventory()->getItemInHand();
                $this->handleFreezingSword($damager, $victim, $this);
            }
        }
        return parent::onAttackEntity($victim, $returnedItems);
    }

    private function handleFreezingSword(Player $damager, Player $entity, Item $item): void {
        if (TimerAPI::hasCooldown($damager, $item)) {
            $timeRemaining = TimerAPI::getCooldownTimeRemaining($damager, $item);
            $damager->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §cThe Freezing Sword is on cooldown for {$timeRemaining} seconds.");
            return;
        }

        $playerName = $entity->getName();
        $this->freezePlayer($entity, 5);
        TimerAPI::startCooldown($damager, 15, $item);
        $damager->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §b{$playerName} has been frozen for 5 seconds!");
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

    private function isPlayer($entity): bool {
        return $entity instanceof Player;
    }
}