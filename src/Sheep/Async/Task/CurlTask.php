<?php
declare(strict_types=1);


namespace Sheep\Async\Task;


use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use Sheep\Async\AsyncMixin;

class CurlTask extends AsyncTask {
	use AsyncMixin;

	private $url;
	private $type;
	private $timeout;

	private $args;
	private $headers;

	public function __construct(
		string $url,
		int $type,
		callable $callback,
		int $timeout = 10,
		array $headers = [],
		array $args = []
	) {
		$this->storeLocal($callback);

		$this->url = $url;
		$this->type = $type;
		$this->timeout = $timeout;
		$this->headers = (array)$headers;
		$this->args = (array)$args;
	}

	public function onRun() {
		$error = null;
		$result = $this->docURL($this->url, $this->type, $this->timeout, $this->headers, $this->args, $error);
		$this->setResult([$result, $error]);
	}

	public function onCompletion(Server $server) {
		($this->fetchLocal())(...$this->getResult());
	}
}
