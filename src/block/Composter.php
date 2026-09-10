<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\data\runtime\RuntimeDataDescriber;

final class Composter extends Transparent{
	private int $fillLevel = 0;

	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$w->boundedIntAuto(0, 8, $this->fillLevel);
	}

	public function getFillLevel() : int{ return $this->fillLevel; }
	public function setFillLevel(int $fillLevel) : self{ $this->fillLevel = $fillLevel; return $this; }
}
