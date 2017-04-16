<?php


namespace Sheep\Task;


use pocketmine\Server;
use pocketmine\utils\Utils;

class CurlTask extends AsyncCallbackTask {
	private $url;
	private $timeout;
	private $headers;

	public function __construct(string $url, callable $callback, int $timeout = 10, array $headers = []) {
		$this->url = $url;
		$this->timeout = $timeout;
		$this->headers = (array) $headers;

		parent::__construct($callback);
	}

	public function onRun() {
		$data = Utils::getURL($this->url, $this->timeout, $this->headers, $error);
		$this->setResult([$data, $error]);
	}

	public function onCompletion(Server $server) {
		/** @var callable $callback */
		$this->callback(...$this->getResult());
	}
}