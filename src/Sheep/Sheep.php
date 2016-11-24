<?php


namespace Sheep;


use Sheep\Command\InstallCommand;
use Sheep\Command\SheepCommand;
use Sheep\Source\Source;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Sheep\Source\SourceManager;

class Sheep extends PluginBase {

	/** @var Config */
	private $cache;
	/** @var SourceManager */
	private $sourceManager;

	public function onEnable() {
		$this->sourceManager = new SourceManager($this);

		@mkdir($this->getDataFolder());
		$this->cache = new Config($this->getDataFolder() . "cache.json", Config::JSON, []);

		$this->getServer()->getCommandMap()->registerAll(
			"s", [
				new SheepCommand($this),
				new InstallCommand($this)
			]
		);
	}

	public function install(Source $source, string $identifier, callable $callback) {

	}

	public function search(string $query, callable $callback, string $source = "Forums") {
		$this->getSourceManager()->get($source)->search($query, $callback);
	}

	public function getSourceManager() {
		return $this->sourceManager;
	}

	public function getGitRevision() {
		$ref = @file_get_contents($this->getFile() . ".git/HEAD");
		if(!$ref) return "unknown";
		$rev = @file_get_contents($this->getFile() . ".git/" . substr($ref, 4));
		return $rev ? $rev : "unknown";
	}

	public function getCache() {
		return $this->cache;
	}

	public function onDisable() {
		$this->getCache()->save();
	}
}