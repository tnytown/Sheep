<?php


namespace Sheep\Utils;


use pocketmine\Server;
use Sheep\Task\CurlTask;
use Sheep\Task\FileWriteTask;

class Utils {
	public static function writeFile(string $path, string $contents, callable $callback) {
		Server::getInstance()->getScheduler()->scheduleAsyncTask(new FileWriteTask($path, $contents, $callback));
	}

	public static function curlGet(string $url, callable $callback, int $timeout = 10, array $headers = []) {
		Server::getInstance()->getScheduler()->scheduleAsyncTask(new CurlTask($url, $callback, $timeout, $headers));
	}

	public static function noop() {}
}