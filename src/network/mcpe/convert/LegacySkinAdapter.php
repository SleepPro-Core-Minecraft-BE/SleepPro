<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use pocketmine\entity\InvalidSkinException;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\protocol\types\skin\SkinData;
use pocketmine\network\mcpe\protocol\types\skin\SkinImage;
use function is_array;
use function is_string;
use function json_decode;
use function json_encode;
use const JSON_THROW_ON_ERROR;

class LegacySkinAdapter implements SkinAdapter{
	public function toSkinData(Skin $skin) : SkinData{
		$capeData = $skin->getCapeData();
		$capeImage = $capeData === "" ? new SkinImage(0, 0, "") : new SkinImage(32, 64, $capeData);
		$geometryName = $skin->getGeometryName();
		if($geometryName === ""){
			$geometryName = "geometry.humanoid.custom";
		}
		$geometryData = $skin->getGeometryData();
		if($geometryData === ""){
			//the client drops the connection if it receives an empty geometry string
			$geometryData = "null";
		}
		return new SkinData(
			$skin->getSkinId(),
			"", //TODO: playfab ID
			json_encode(["geometry" => ["default" => $geometryName]], JSON_THROW_ON_ERROR),
			SkinImage::fromLegacy($skin->getSkinData()),
			[],
			$capeImage,
			$geometryData
		);
	}

	public function fromSkinData(SkinData $data) : Skin{
		if($data->isPersona()){
			$skinImage = $data->getSkinImage();
			$coreSkinData = self::resizeSkinImageToClassic(
				$skinImage->getData(),
				$skinImage->getWidth(),
				$skinImage->getHeight()
			);
			$skinId = $data->getSkinId();
			return new Skin($skinId, $coreSkinData);
		}

		$capeData = $data->isPersonaCapeOnClassic() ? "" : $data->getCapeImage()->getData();

		$resourcePatch = json_decode($data->getResourcePatch(), true);
		if(is_array($resourcePatch) && isset($resourcePatch["geometry"]["default"]) && is_string($resourcePatch["geometry"]["default"])){
			$geometryName = $resourcePatch["geometry"]["default"];
		}else{
			throw new InvalidSkinException("Missing geometry name field");
		}

		return new Skin($data->getSkinId(), $data->getSkinImage()->getData(), $capeData, $geometryName, $data->getGeometryDataJson());
	}

	/**
	 * Creates a valid classic preview for APIs which only understand classic skins.
	 * The converted image is also sent back to clients as a classic skin. This avoids
	 * disconnects caused by Character Creator pieces unknown to another client build.
	 */
	private static function resizeSkinImageToClassic(string $source, int $width, int $height) : string{
		if($width === 64 && ($height === 32 || $height === 64)){
			return $source;
		}

		$targetSize = 128;
		$result = "";
		for($y = 0; $y < $targetSize; ++$y){
			$sourceY = (int) ($y * $height / $targetSize);
			for($x = 0; $x < $targetSize; ++$x){
				$sourceX = (int) ($x * $width / $targetSize);
				$offset = ($sourceY * $width + $sourceX) * 4;
				$result .= $source[$offset] . $source[$offset + 1] . $source[$offset + 2] . $source[$offset + 3];
			}
		}
		return $result;
	}
}
