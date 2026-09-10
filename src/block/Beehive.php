<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\data\runtime\RuntimeDataDescriber;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\Shears;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\sound\BeehiveShearSound;

final class Beehive extends Opaque{
	private int $direction = 0;
	private int $honeyLevel = 0;

	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$w->boundedIntAuto(0, 3, $this->direction);
		$w->boundedIntAuto(0, 5, $this->honeyLevel);
	}

	public function getDirection() : int{ return $this->direction; }
	public function setDirection(int $direction) : self{ $this->direction = $direction; return $this; }
	public function getHoneyLevel() : int{ return $this->honeyLevel; }
	public function setHoneyLevel(int $honeyLevel) : self{ $this->honeyLevel = $honeyLevel; return $this; }

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null, array &$returnedItems = []) : bool{
		if($this->honeyLevel < 5){
			return false;
		}
		if($item instanceof Shears){
			$item->applyDamage(1);
			for($i = 0; $i < 3; ++$i){
				$returnedItems[] = VanillaItems::HONEYCOMB();
			}
		}elseif($item->getTypeId() === ItemTypeIds::GLASS_BOTTLE){
			$item->pop();
			$returnedItems[] = VanillaItems::HONEY_BOTTLE();
		}else{
			return false;
		}
		$this->position->getWorld()->setBlock($this->position, $this->setHoneyLevel(0));
		$this->position->getWorld()->addSound($this->position, new BeehiveShearSound());
		return true;
	}
}
