<?php

declare(strict_types=1);

namespace pocketmine\world\sound;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;

final class ComposterSound implements Sound{
	public const EMPTY = LevelSoundEvent::BLOCK_COMPOSTER_EMPTY;
	public const FILL = LevelSoundEvent::BLOCK_COMPOSTER_FILL;
	public const FILL_SUCCESS = LevelSoundEvent::BLOCK_COMPOSTER_FILL_SUCCESS;
	public const READY = LevelSoundEvent::BLOCK_COMPOSTER_READY;

	public function __construct(private string $sound){}

	public function encode(Vector3 $pos) : array{
		return [LevelSoundEventPacket::nonActorSound($this->sound, $pos, false)];
	}
}
