<?php
declare(strict_types=1);


namespace Sheep\Async;


use React\Promise\Deferred;
use React\Promise\Promise;

class CLIAsyncHandler implements AsyncHandler {
	use AsyncMixin;

	public function getURL(string $url, int $timeout = 10, array $extraHeaders = []): Promise {
		$deferred = new Deferred();

		$result = $this->docURL($url, CurlOptions::CURL_GET, $timeout, $extraHeaders, [], $error);
		if ($error !== "") {
			$deferred->reject($error);
		} else {
			$deferred->resolve($result);
		}

		return $deferred->promise();
	}

	public function read(string $file): Promise {
		$deferred = new Deferred();

		$result = $this->readFile($file);
		if (!$result) {
			$deferred->reject();
		} else {
			$deferred->resolve($result);
		}

		return $deferred->promise();
	}

	public function write(string $file, string $data): Promise {
		$deferred = new Deferred();

		$result = $this->writeFile($file, $data);
		if (!$result) {
			$deferred->reject();
		} else {
			$deferred->resolve($result);
		}

		return $deferred->promise();
	}
}