<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Items;

use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use customiesdevs\customies\item\CreativeInventoryInfo;
use pocketmine\world\World;
use pocketmine\math\VoxelRayTrace;
use AmitxD\CustomItem\libs\TimerAPI\TimerAPI;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\TieredTool;
use pocketmine\item\ToolTier;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\player\Player;

class TeleportationSword extends TieredTool implements ItemComponents {
    use ItemComponentsTrait;

    public function __construct(ItemIdentifier $identifier, string $name = "TeleportationSword") {
        parent::__construct($identifier, $name, ToolTier::DIAMOND());
        $creativeInv = new CreativeInventoryInfo(CreativeInventoryInfo::GROUP_SWORD);
        $this->initComponent("TeleportationSword", $creativeInv);
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

    public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems) : ItemUseResult {
        $this->handleTeleportationSword($player, $this);
        return parent::onClickAir($player, $directionVector, $returnedItems);
    }

    private function handleTeleportationSword(Player $player, Item $item): void {
        if (TimerAPI::hasCooldown($player, $item)) {
            $timeRemaining = TimerAPI::getCooldownTimeRemaining($player, $item);
            $player->sendMessage("§r[§dOMNI§bCRAFT§r] §c>>§r §cThe TeleportationSword is on cooldown for {$timeRemaining} seconds.");
            return;
        }
        $start = $player->getPosition()->add(0, $player->getEyeHeight(), 0);
        $end = $start->addVector($player->getDirectionVector()->multiply($player->getViewDistance() * 18));
        $world = $player->getWorld();
        foreach (VoxelRayTrace::betweenPoints($start, $end) as $vector3) {
            TimerAPI::startCooldown($player, 2, $item);
            if ($vector3->y >= World::Y_MAX or $vector3->y <= 0) {
                return;
            }
            if (($result = $world->getBlock($vector3)->calculateIntercept($start, $end)) !== null) {
                $target = $result->hitVector;
                $player->teleport($target);
                return;
            }
        }
    }
}