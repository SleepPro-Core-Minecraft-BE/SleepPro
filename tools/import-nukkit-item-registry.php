<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;

if(count($argv) !== 4){
	fwrite(STDERR, "Usage: php import-nukkit-item-registry.php <runtime-items.json> <item-components.nbt> <output.json>\n");
	exit(1);
}

[$script, $itemsPath, $componentsPath, $outputPath] = $argv;
$items = json_decode(file_get_contents($itemsPath), true, flags: JSON_THROW_ON_ERROR);
$compressedComponents = file_get_contents($componentsPath);
$componentsRaw = $compressedComponents === false ? false : zlib_decode($compressedComponents);
if(!is_array($items) || $componentsRaw === false){
	throw new RuntimeException('Invalid Nukkit registry input');
}

$components = (new BigEndianNbtSerializer())->read($componentsRaw)->mustGetCompoundTag();
$littleEndian = new LittleEndianNbtSerializer();
$result = [];
foreach($items as $entry){
	$name = $entry['name'];
	$componentNbt = $components->getCompoundTag($name);
	$result[$name] = [
		'runtime_id' => $entry['id'],
		'component_based' => $entry['componentBased'],
		'version' => $entry['version'],
	];
	if($componentNbt !== null){
		$result[$name]['component_nbt'] = base64_encode($littleEndian->write(new TreeRoot($componentNbt)));
	}
}

$encoded = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";
if(file_put_contents($outputPath, $encoded) === false){
	throw new RuntimeException("Unable to write $outputPath");
}
printf("Imported %d item definitions to %s\n", count($result), $outputPath);
