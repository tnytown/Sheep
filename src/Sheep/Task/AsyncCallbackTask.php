<?php


namespace Sheep\Task;


use pocketmine\scheduler\AsyncTask;

abstract class AsyncCallbackTask extends AsyncTask {
	protected static $callbackList;

	protected function callback(...$args) {
		call_user_func_array($this->fetchLocal(), $args);
	}
}