<?php


namespace Sheep\Task;


use pocketmine\Server;

class FileGetTask extends AsyncCallbackTask {
	protected static $callbackList = [];
	private $file;


	public function __construct(string $file, callable $callback) {
		$this->file = $file;

		$this->setCallback($callback);
	}

	public function onRun() {
		$this->setResult(@file_get_contents($this->file));
	}

	public function onCompletion(Server $server) {
		$this->callback($this->getTaskId(), $this->getResult());
	}
}