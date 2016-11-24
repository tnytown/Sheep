<?php


namespace Sheep\Task;


use pocketmine\Server;

class FileWriteTask extends AsyncCallbackTask {
	protected static $callbackList = [];

	private $path;
	private $contents;

	public function __construct($path, $contents, callable $callback){
		$this->path = $path;
		$this->contents = $contents;

		$this->setCallback($callback);
	}

	public function onRun(){
		try {
			@file_put_contents($this->path, $this->contents);
		} catch (\Throwable $e) {}
		$this->setResult($this->path);
	}

	public function onCompletion(Server $server) {
		$this->callback($this->getTaskId(), $this->getResult());
	}
}