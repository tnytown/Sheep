<?php
declare(strict_types = 1);


namespace Sheep\Async;


use pocketmine\scheduler\ServerScheduler;
use React\Promise\Deferred;
use React\Promise\Promise;
use Sheep\Async\Task\CurlTask;
use Sheep\Async\Task\WriteTask;

class PMAsyncHandler implements AsyncHandler {
	private $scheduler;

	public function __construct(ServerScheduler $scheduler) {
		$this->scheduler = $scheduler;
	}

	public function getURL(string $url, int $timeout = 10, array $extraHeaders = []): Promise {
		$deferred = new Deferred();

		$this->scheduler->scheduleAsyncTask(new CurlTask($url, CurlOptions::CURL_GET,
			function(string $result, string $error) use (&$deferred) {
			if($error) {
				$deferred->reject($error);
			} else {
				$deferred->resolve($result);
			}
		}, $timeout, $extraHeaders));

		return $deferred->promise();
	}

	public function read(string $file): Promise {
		// TODO: Implement read() method.
	}

	public function write(string $file, string $data): Promise {
		$deferred = new Deferred();

		$this->scheduler->scheduleAsyncTask(new WriteTask($file, $data,
			function(bool $error) use (&$deferred) {
				if(!$error) {
					$deferred->reject($error);
				} else {
					$deferred->resolve();
				}
			}));

		return $deferred->promise();
	}
}