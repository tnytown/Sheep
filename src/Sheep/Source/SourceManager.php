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

	public function find(string $query) {
		foreach($this->sources as $source) {
			if(preg_match($source->regex(), $query)) return $source;
		}
		return $this->get(Forums::class);
	}

	public function register(string $name, Source $source) {
		if(preg_match("/@deprecated/", (new \ReflectionClass($source))->getDocComment()) > 0) {
			trigger_error("The source $name is deprecated. Using this is not a good idea!", E_USER_DEPRECATED);
		}

		$this->sources[$name] = $source;
	}

	public function registerDefaults() {
		$this->register(Forums::class, new Forums($this->plugin));
	}

	public function getFallbackSource() {
		return $this->sources[0];
	}
}