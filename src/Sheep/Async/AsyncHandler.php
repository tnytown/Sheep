<?php
declare(strict_types=1);


namespace Sheep\Async;


use React\Promise\Promise;

interface AsyncHandler {

	public function getURL(string $url, int $timeout = 10, array $extraHeaders = []): Promise;

	public function read(string $file): Promise;

	public function write(string $file, string $data): Promise;
}