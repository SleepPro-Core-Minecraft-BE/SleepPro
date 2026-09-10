<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\data\runtime\RuntimeDataDescriber;

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
}
