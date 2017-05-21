<?php


namespace Sheep\Async\Task;


use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use Sheep\Async\AsyncMixin;

class WriteTask extends AsyncTask {
	use AsyncMixin;

	private $location, $contents;

	public function __construct(string $location, string $contents, callable $callback) {
		parent::__construct($callback);

		$this->location = $location;
		$this->contents = $contents;
	}

	public function onRun() {
		$this->setResult($this->writeFile($this->location, $this->contents));
	}

	public function onCompletion(Server $server) {
		($this->fetchLocal())($this->getResult());
	}
}