<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\data\runtime\RuntimeDataDescriber;

final class Scaffolding extends Transparent{
	private int $stability = 0;
	private bool $needsStabilityCheck = false;

	protected function describeBlockOnlyState(RuntimeDataDescriber $w) : void{
		$w->boundedIntAuto(0, 7, $this->stability);
		$w->bool($this->needsStabilityCheck);
	}

	public function getStability() : int{ return $this->stability; }
	public function setStability(int $stability) : self{ $this->stability = $stability; return $this; }
	public function needsStabilityCheck() : bool{ return $this->needsStabilityCheck; }
	public function setNeedsStabilityCheck(bool $value) : self{ $this->needsStabilityCheck = $value; return $this; }
}
