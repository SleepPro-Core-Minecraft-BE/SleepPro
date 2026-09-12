<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\inventory\ArmorInventory;
use pocketmine\item\enchantment\ItemEnchantmentTags;

final class Elytra extends Armor{
	public function __construct(ItemIdentifier $identifier, string $name){
		parent::__construct(
			$identifier,
			$name,
			new ArmorTypeInfo(0, 432, ArmorInventory::SLOT_CHEST, material: new ArmorMaterial(1)),
			[ItemEnchantmentTags::ELYTRA]
		);
	}

	public function isUsable() : bool{
		return $this->getDamage() < $this->getMaxDurability() - 1;
	}

	public function applyFlightDamage() : void{
		if($this->isUsable()){
			$this->setDamage(min($this->getDamage() + 1, $this->getMaxDurability() - 1));
		}
	}
}
