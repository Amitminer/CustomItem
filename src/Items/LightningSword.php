<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Items;

use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use customiesdevs\customies\item\CreativeInventoryInfo;
use AmitxD\CustomItem\libs\TimerAPI\TimerAPI;
use pocketmine\event\entity\EntityDamageByEntityEvent as EntityDamage;
use AmitxD\CustomItem\Utils\Utils;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\TieredTool;
use pocketmine\item\Item;
use pocketmine\item\ToolTier;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\player\Player;

class LightningSword extends TieredTool implements ItemComponents {
    use ItemComponentsTrait;

    public function __construct(ItemIdentifier $identifier, string $name = "LightningSword") {
        parent::__construct($identifier, $name, ToolTier::DIAMOND());
        $creativeInv = new CreativeInventoryInfo(CreativeInventoryInfo::GROUP_SWORD);
        $this->initComponent("LightningSword", $creativeInv);
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
        $lastDamageCause = $victim->getLastDamageCause();
        if ($lastDamageCause instanceof EntityDamage) {
            $damager = $lastDamageCause->getDamager();
            if ($this->isPlayer($damager)) {
                $this->handleLightningSword($damager, $victim, $this);
            }
        }
        return parent::onAttackEntity($victim, $returnedItems);
    }

    private function handleLightningSword(Player $damager, Player $entity, Item $item): void {
        if (TimerAPI::hasCooldown($damager, $item)) {
            $timeRemaining = TimerAPI::getCooldownTimeRemaining($damager, $item);
            $damager->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §cThe Lightning Sword is on cooldown for {$timeRemaining} seconds.");
            return;
        }

        Utils::summonLightning($entity);
        TimerAPI::startCooldown($damager, 15, $item);
    }

    private function isPlayer($entity): bool {
        return $entity instanceof Player;
    }

}
