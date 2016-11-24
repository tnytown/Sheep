<?php


namespace Sheep;


use pocketmine\plugin\PluginDescription;
use Sheep\Source\Source;
use Sheep\Task\FileGetTask;
use Sheep\Task\FileWriteTask;
use pocketmine\Server;

class Plugin implements \JsonSerializable {

	/** @var Source */
	private $source;
	/** @var string */
	private $name;
	/** @var string */
	private $authors;
	/** @var int */
	private $version;

	private $installed = false;

	private $metadata = [];

	public $uri;

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getAuthor() {
		return $this->authors;
	}

	/**
	 * @return int
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * @return bool
	 */
	public function isInstalled() {
		return $this->installed;
	}

	public function __construct(Source $source) {
		$this->source = $source;
	}

	public function install(callable $callback) {
		$this->source->install($this, $callback);
	}

	public function loadDescription(PluginDescription $desc) {
		$this->name = $desc->getName();
		$this->authors = $desc->getAuthors();
		$this->version = $desc->getVersion();
	}

	public function __get($name) {
		if(!isset($this->metadata[$name])) {
			return null;
		}
		return $this->metadata[$name];
	}

	public function __set($name, $value) {
		$this->metadata[$name] = $value;
	}

	public function jsonSerialize() {
		return [
			"source" => get_class($this->source),
			"data" => $this->metadata,
			"name" => $this->name,
			"author" => $this->authors,
			"version" => $this->version,
			"installed" => $this->installed,
		];
	}
}