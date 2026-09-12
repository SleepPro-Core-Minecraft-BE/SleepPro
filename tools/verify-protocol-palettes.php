<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use pocketmine\data\bedrock\block\BlockStateData;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\network\mcpe\convert\BlockTranslator;
use pocketmine\network\mcpe\convert\ItemTypeDictionaryFromDataHelper;
use pocketmine\network\mcpe\protocol\ProtocolInfo;

if(!extension_loaded('encoding')){
	throw new RuntimeException('The ext-encoding extension from the PocketMine PHP binary is required');
}

if(ProtocolInfo::CURRENT_PROTOCOL !== ProtocolInfo::PROTOCOL_1_26_45){
	throw new RuntimeException('The current protocol is not Minecraft 1.26.45');
}

foreach(ProtocolInfo::ACCEPTED_PROTOCOL as $protocolId){
	$blockTranslator = BlockTranslator::loadFromProtocolId($protocolId);
	$itemDictionary = ItemTypeDictionaryFromDataHelper::loadFromProtocolId($protocolId);

	if(count($itemDictionary->getEntries()) === 0){
		throw new RuntimeException("Protocol $protocolId has an empty item dictionary");
	}
	if(count($blockTranslator->getBlockStateDictionary()->getStates()) === 0){
		throw new RuntimeException("Protocol $protocolId has an empty or invalid block palette");
	}
}

$requiredCurrentProtocols = [
	ProtocolInfo::PROTOCOL_1_26_44,
	ProtocolInfo::PROTOCOL_1_26_45,
];
foreach($requiredCurrentProtocols as $protocolId){
	if(!in_array($protocolId, ProtocolInfo::ACCEPTED_PROTOCOL, true)){
		throw new RuntimeException("Protocol $protocolId is not accepted by the login handler");
	}
}

$palette44 = BlockTranslator::loadFromProtocolId(ProtocolInfo::PROTOCOL_1_26_44);
$palette45 = BlockTranslator::loadFromProtocolId(ProtocolInfo::PROTOCOL_1_26_45);
$items44 = ItemTypeDictionaryFromDataHelper::loadFromProtocolId(ProtocolInfo::PROTOCOL_1_26_44);
$items45 = ItemTypeDictionaryFromDataHelper::loadFromProtocolId(ProtocolInfo::PROTOCOL_1_26_45);
$expectedAirNetworkId = -604749536;

if(
	count($items44->getEntries()) !== count($items45->getEntries()) ||
	count($palette44->getBlockStateDictionary()->getStates()) !== count($palette45->getBlockStateDictionary()->getStates()) ||
	$palette44->getBlockStateDictionary()->lookupStateIdFromData(BlockStateData::current(BlockTypeNames::AIR, [])) !== $expectedAirNetworkId ||
	$palette45->getBlockStateDictionary()->lookupStateIdFromData(BlockStateData::current(BlockTypeNames::AIR, [])) !== $expectedAirNetworkId
){
	throw new RuntimeException('Minecraft 1.26.44 and 1.26.45 must use the same hashed Bedrock registries');
}

printf(
	"Verified %d block palettes and item dictionaries (%d through %d); 1.26.44/2168 and 1.26.45/2169 are enabled.\n",
	count(ProtocolInfo::ACCEPTED_PROTOCOL),
	ProtocolInfo::PROTOCOL_1_20_0,
	ProtocolInfo::PROTOCOL_1_26_45,
);
