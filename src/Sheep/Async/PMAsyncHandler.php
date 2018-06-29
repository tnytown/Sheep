<?php
/**
 * Copyright (c) 2017, 2018 KnownUnown
 *
 * This file is part of Sheep.
 *
 * Sheep is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Sheep is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);


namespace Sheep\Async;


use pocketmine\scheduler\AsyncPool;
use React\Promise\Deferred;
use React\Promise\Promise;
use Sheep\Async\Task\CurlTask;
use Sheep\Async\Task\WriteTask;

class PMAsyncHandler implements AsyncHandler {
	private $async;

	public function __construct(AsyncPool $async) {
		$this->async = $async;
	}

	public function getURL(string $url, int $timeout = 10, array $extraHeaders = []): Promise {
		$deferred = new Deferred();

		$this->async->submitTask(new CurlTask($url, CurlOptions::CURL_GET,
			function(string $result, string $error) use (&$deferred) {
				if($error) {
					$deferred->reject($error);
				} else {
					$deferred->resolve($result);
				}
			}, $timeout, $extraHeaders, [], \Sheep\VERSION, \Sheep\VARIANT));

		return $deferred->promise();
	}

	public function read(string $file): Promise {
		// TODO: Implement read() method.
	}

	public function write(string $file, string $data): Promise {
		$deferred = new Deferred();

		$this->async->submitTask(new WriteTask($file, $data,
			function(bool $ok) use (&$deferred) {
				if(!$ok) {
					$deferred->reject($ok);
				} else {
					$deferred->resolve();
				}
			}));

		return $deferred->promise();
	}
}
