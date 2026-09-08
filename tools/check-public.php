<?php
declare(strict_types=1);
require dirname(__DIR__) . '/vendor/autoload.php';
use pocketmine\VersionInfo;
use pocketmine\network\mcpe\convert\BlockTranslator;
use pocketmine\network\mcpe\convert\ItemTypeDictionaryFromDataHelper;
use pocketmine\network\mcpe\convert\ItemTranslator;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
if(VersionInfo::BASE_VERSION !== '5.44.2' || VersionInfo::PUBLIC_VERSION !== '0.1-PUBLIC'){
    throw new RuntimeException('Unexpected API or product version');
}
foreach(ProtocolInfo::ACCEPTED_PROTOCOL as $protocol){
    BlockTranslator::loadFromProtocolId($protocol);
    ItemTypeDictionaryFromDataHelper::loadFromProtocolId($protocol);
    ItemTranslator::getItemSchemaId($protocol);
    TypeConverter::getInstance($protocol);
    echo "Palette and item schema: $protocol OK\n";
}
if(!is_file(dirname(__DIR__) . '/resources/settings.yml')){
    throw new RuntimeException('Missing settings.yml template');
}
echo 'PUBLIC checks passed; API ' . VersionInfo::BASE_VERSION . PHP_EOL;
