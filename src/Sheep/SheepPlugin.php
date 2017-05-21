<?php
declare(strict_types = 1);


namespace Sheep;


use Sheep\Command\InstallCommand;
use Sheep\Command\SearchCommand;
use Sheep\Command\SheepCommand;
use Sheep\Source\Source;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Sheep\Source\SourceManager;

class SheepPlugin extends PluginBase {

	/** @var Config */
	private $cache;
	/** @var SourceManager */
	private $sourceManager;

	/**
	 *
	 */
	public function onEnable() {
		define("Sheep\\GIT_REVISION", $this->getGitRevision());
		$this->sourceManager = new SourceManager($this);
		$this->sourceManager->registerDefaults();

		@mkdir($this->getDataFolder());
		$this->cache = new Config($this->getDataFolder() . "cache.json", Config::JSON, []);
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
		$ref = @file_get_contents($this->getFile() . DIRECTORY_SEPARATOR . ".git/HEAD");
		if(!$ref) return "unknown";
		$rev = trim(@file_get_contents($this->getFile() . ".git/" . trim(explode(" ", $ref)[1])));
		return $rev ?: "unknown";
	}

	public function getCache() {
		return $this->cache;
	}

	public function onDisable() {
		$this->getCache()->save();
	}
}