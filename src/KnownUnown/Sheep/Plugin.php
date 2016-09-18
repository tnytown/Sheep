<?php


namespace KnownUnown\Sheep;


use KnownUnown\Sheep\Source\Source;
use KnownUnown\Sheep\Task\FileGetTask;
use KnownUnown\Sheep\Task\FileWriteTask;
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

	public function __construct(Source $source) {
		$this->source = $source;
	}

	public function install(callable $callback) {
		if(isset($this->uri)) {
			$name = md5($this->uri);

			Server::getInstance()->getScheduler()->scheduleAsyncTask(
				$read = new FileGetTask($this->uri, function($taskId, $result) use ($callback, $name) {
					if($result) {
						Server::getInstance()->getScheduler()->scheduleAsyncTask(
							$write = new FileWriteTask(\pocketmine\PLUGIN_PATH . DIRECTORY_SEPARATOR . $name, $result, function($taskId, $path) {
								$plugin = Server::getInstance()->getPluginManager()->loadPlugin($path)->getDescription();

								$this->name = $plugin->getName();
								$this->authors = $plugin->getAuthors();
								$this->version = $plugin->getVersion();
							})
						);
					}
				}, $this)
			);
		}
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
		];
	}
}