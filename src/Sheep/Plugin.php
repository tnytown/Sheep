<?php


namespace Sheep;


use pocketmine\plugin\PluginDescription;
use Sheep\Source\Source;
use Sheep\Task\CurlTask;
use Sheep\Task\FileWriteTask;
use pocketmine\Server;

abstract class Plugin implements \JsonSerializable {
	protected $source;
	protected $name;
	protected $authors;
	protected $version;

	protected $dependencies;
	protected $installed = false;

	protected $uri;

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

	/**
	 * @return bool
	 */
	public function isInstalled() : bool {
		return $this->installed;
	}

	public function getUri() : string {
		return $this->uri;
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
			"installed" => $this->installed,
		];
	}
}