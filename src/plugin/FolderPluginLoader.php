<?php

declare(strict_types=1);

namespace pocketmine\plugin;

use pocketmine\thread\ThreadSafeClassLoader;
use Symfony\Component\Filesystem\Path;
use function file_get_contents;
use function is_dir;
use function is_file;

/** Loads development plugins directly from directories containing plugin.yml and src/. */
final class FolderPluginLoader implements PluginLoader{
	public function __construct(
		private ThreadSafeClassLoader $loader
	){}

	public function canLoadPlugin(string $path) : bool{
		return is_dir($path) && is_file(Path::join($path, "plugin.yml"));
	}

	public function loadPlugin(string $file) : void{
		$description = $this->getPluginDescription($file);
		if($description !== null){
			$this->loader->addPath($description->getSrcNamespacePrefix(), Path::join($file, "src"));
		}
	}

	public function getPluginDescription(string $file) : ?PluginDescription{
		$manifest = Path::join($file, "plugin.yml");
		if(!is_file($manifest)){
			return null;
		}

		$contents = file_get_contents($manifest);
		if($contents === false){
			throw new \RuntimeException("Unable to read plugin manifest $manifest");
		}
		return new PluginDescription($contents);
	}

	public function getAccessProtocol() : string{
		return "";
	}
}
