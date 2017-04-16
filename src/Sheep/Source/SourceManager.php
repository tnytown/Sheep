<?php


namespace Sheep\Source;


use Sheep\Sheep;

class SourceManager {
	private $plugin;
	/** @var Source[] */
	private $sources;

	public function __construct(Sheep $plugin) {
		$this->plugin = $plugin;
		$this->sources = [];
	}

	public function get(string $name) {
		return isset($this->sources[$name]) ? $this->sources[$name] : false;
	}

	public function register(string $name, Source $source) {
		if(preg_match("/@deprecated/", (new \ReflectionClass($source))->getDocComment()) > 0) {
			trigger_error("The source $name is deprecated. Using this is not a good idea!", E_USER_DEPRECATED);
		}

		$this->sources[$name] = $source;
	}

	public function registerDefaults() {
		$this->register("Poggit", new Poggit($this->plugin->getServer()->getPluginManager()));
	}

	public function getDefaultSource() {
		return $this->sources["Poggit"];
	}
}