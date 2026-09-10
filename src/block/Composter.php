<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\sound\ComposterSound;

final class Composter extends Transparent{
	private const READY_LEVEL = 8;
	private const FULL_LEVEL = 7;

	private int $fillLevel = 0;

	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$w->boundedIntAuto(0, 8, $this->fillLevel);
	}

	public function getFillLevel() : int{ return $this->fillLevel; }
	public function setFillLevel(int $fillLevel) : self{
		if($fillLevel < 0 || $fillLevel > self::READY_LEVEL){
			throw new \InvalidArgumentException("Fill level must be in range 0 ... " . self::READY_LEVEL);
		}
		$this->fillLevel = $fillLevel;
		return $this;
	}

	private static function getCompostChance(Item $item) : int{
		$chance = match($item->getTypeId()){
			ItemTypeIds::BEETROOT_SEEDS, ItemTypeIds::DRIED_KELP, ItemTypeIds::MELON_SEEDS,
			ItemTypeIds::PUMPKIN_SEEDS, ItemTypeIds::WHEAT_SEEDS => 30,
			ItemTypeIds::SUGAR, ItemTypeIds::MELON,
			ItemTypeIds::SWEET_BERRIES, ItemTypeIds::GLOW_BERRIES => 50,
			ItemTypeIds::APPLE, ItemTypeIds::BEETROOT, ItemTypeIds::CARROT,
			ItemTypeIds::POTATO, ItemTypeIds::WHEAT => 65,
			ItemTypeIds::BREAD, ItemTypeIds::COOKIE => 85,
			ItemTypeIds::PUMPKIN_PIE => 100,
			default => 0
		};

		if($chance === 0 && $item instanceof ItemBlock){
			$block = $item->getBlock();
			$chance = match(true){
				$block instanceof Sapling, $block instanceof Leaves, $block instanceof Flower,
				$block instanceof TallGrass, $block instanceof MossCarpet => 30,
				$block instanceof Cactus, $block instanceof Sugarcane, $block instanceof Vine,
				$block->getTypeId() === BlockTypeIds::MOSS_BLOCK => 50,
				$block instanceof HayBale => 85,
				default => 0
			};
		}
		return $chance;
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []) : bool{
		$world = $this->position->getWorld();
		if($this->fillLevel === self::READY_LEVEL){
			$returnedItems[] = VanillaItems::BONE_MEAL();
			$world->setBlock($this->position, $this->setFillLevel(0));
			$world->addSound($this->position, new ComposterSound(ComposterSound::EMPTY));
			return true;
		}

		$chance = self::getCompostChance($item);
		if($chance === 0 || $this->fillLevel >= self::FULL_LEVEL){
			return false;
		}

		$item->pop();
		$success = random_int(1, 100) <= $chance;
		if($success){
			$world->setBlock($this->position, $this->setFillLevel($this->fillLevel + 1));
			if($this->fillLevel === self::FULL_LEVEL){
				$world->scheduleDelayedBlockUpdate($this->position, 20);
			}
		}
		$world->addSound($this->position, new ComposterSound($success ? ComposterSound::FILL_SUCCESS : ComposterSound::FILL));
		return true;
	}

	public function onScheduledUpdate() : void{
		if($this->fillLevel === self::FULL_LEVEL){
			$this->position->getWorld()->setBlock($this->position, $this->setFillLevel(self::READY_LEVEL));
			$this->position->getWorld()->addSound($this->position, new ComposterSound(ComposterSound::READY));
		}
	}
}
