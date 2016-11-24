<?php


namespace Sheep\Task;


use pocketmine\scheduler\AsyncTask;

abstract class AsyncCallbackTask extends AsyncTask {
	protected static $callbackList;

	protected function callback(...$args) {
		if(isset(static::$callbackList[$id = spl_object_hash($this)])) {
			call_user_func_array(static::$callbackList[$id], $args);
			unset(static::$callbackList[$id]);
			return;
		}
	}

	protected function setCallback(callable $callback) {
		static::$callbackList[spl_object_hash($this)] = $callback;
	}
}