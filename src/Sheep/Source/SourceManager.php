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
		$this->sources[$name] = $source;
	}

	public function registerDefaults() {
		$this->register(Forums::class, new Forums($this->plugin));
	}
}