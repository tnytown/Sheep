<?php
declare(strict_types = 1);


namespace Sheep;


abstract class Plugin implements \JsonSerializable{
	protected $source;
	protected $name;
	protected $authors;

	protected $version;
	protected $dependencies;

	protected $uri;
	protected $state = PluginState::STATE_NOT_INSTALLED;

	/**
	 * Returns human-readable, formatted info on the plugin.
	 * @return string
	 */
	public function __toString() : string {
		return "@$this->source/$this->name:$this->version";
	}

	public function getName() : string {
		return $this->name;
	}

	public function getAuthors() : array {
		return $this->authors;
	}

	public function getVersion() : string {
		return $this->version;
	}

	public function getDependencies() : array {
		return $this->dependencies;
	}

	public function getUri() : string {
		return $this->uri;
	}

	public function getState() {
		return $this->state;
	}

	public function setState($state) {
		$this->state = $state;
	}

	public function __construct(string $source) {
		$this->source = $source;
	}

	public function jsonSerialize() {
		return [
			"source" => $this->source,
			"name" => $this->name,
			"author" => $this->authors,
			"version" => $this->version,
			"state" => $this->state,
		];
	}
}