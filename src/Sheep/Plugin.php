<?php
declare(strict_types=1);


namespace Sheep;


class Plugin implements \JsonSerializable {
	protected $source;
	protected $name;

	protected $version;
	protected $dependencies;
	protected $info = [];

	protected $uri;
	public $state = PluginState::STATE_NOT_INSTALLED;

	/**
	 * Returns human-readable, formatted info on the plugin.
	 * @return string
	 */
	public function __toString(): string {
		return "@$this->source/$this->name:$this->version";
	}

	public function getName(): string {
		return $this->name;
	}

	public function getVersion(): string {
		return $this->version;
	}

	public function getDependencies(): array {
		return $this->dependencies;
	}

	public function getInfo(): array {
		return $this->info;
	}

	public function getUri(): string {
		return $this->uri;
	}

	public function jsonSerialize() {
		return [
			"source" => $this->source,
			"name" => $this->name,
			"version" => $this->version,
			"state" => $this->state,
		];
	}

	/**
	 * Constructs a plugin from the given data.
	 *
	 * @param array $data
	 * @return Plugin
	 */
	public static function fromArray(array $data): Plugin {
		$plugin = new self();

		$plugin->source = $data["source"];
		$plugin->name = $data["name"];
		$plugin->version = $data["version"];
		$plugin->state = $data["state"];

		$plugin->info = [
			"source" => $plugin->source,
			"name" => $plugin->name,
			"version" => $plugin->version
		];

		return $plugin;
	}

	/**
	 * @return int
	 */
	public function getState(): int {
		return $this->state;
	}

	/**
	 * @param int $state
	 */
	public function setState(int $state) {
		$this->state = $state;
	}
}