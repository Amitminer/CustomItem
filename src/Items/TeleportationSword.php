<?php

declare(strict_types = 1);

namespace AmitxD\CustomItem\Items;

use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use customiesdevs\customies\item\CreativeInventoryInfo;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\TieredTool;
use pocketmine\item\ToolTier;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\player\Player;

class TeleportationSword extends TieredTool implements ItemComponents {
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "TeleportationSword"){
		parent::__construct($identifier, $name, ToolTier::DIAMOND());
		$creativeInv = new CreativeInventoryInfo(CreativeInventoryInfo::GROUP_SWORD);
		$this->initComponent("TeleportationSword", $creativeInv);
	}
	
	public function getMiningEfficiency(bool $isCorrectTool) : float{
		return parent::getMiningEfficiency($isCorrectTool) * 3.5;
	}

	public function onDestroyBlock(Block $block, array &$returnedItems) : bool{
		if(!$block->getBreakInfo()->breaksInstantly()){
			return $this->applyDamage(2);
		}
		return false;
	}

	public function onAttackEntity(Entity $victim, array &$returnedItems) : bool{
		return $this->applyDamage(1);
	}
}
