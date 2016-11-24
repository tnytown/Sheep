<?php

namespace Sheep\Source;

use Sheep\Plugin;
use Sheep\Task\FileGetTask;

class Forums extends BaseSource {

	/** @var callable[] */
	public $callbacks = [];

	const SEARCH_ENDPOINT = "https://pluginsearch.pocketdock.io";
	const FORUMS_ENDPOINT = "https://forums.pocketmine.net/api.php";

	public function search(string $plugin, callable $callback, bool $exact = false) {
		$this->plugin->getServer()->getScheduler()->scheduleAsyncTask(
			$task = new FileGetTask(Forums::SEARCH_ENDPOINT . "/search?q=" . $plugin, function($taskId, $result) {
				$plugins = null;
				if($result && isset($this->callbacks[$taskId])) {
					$plugins = array_map(function(array $value) {
						$data = $value["_source"];

						$plugin = new Plugin($this);
						$plugin->uri = "https://forums.pocketmine.net/plugins/" .
							$data["resource_id"] . "/download?version=" . $data["current_version_id"];

						$plugin->data_title = $data["title"];
						$plugin->data_author = $data["username"];
						$plugin->data_rating = round($data["rating_avg"], 2);
						$plugin->data_description = $data["tag_line"];
						$plugin->data_version_id = $data["current_version_id"];
						$plugin->data_outdated = ($data["prefix_id"] === 7);
						$plugin->data_ttl = time() + (5 * 60 * 60); // 5 minutes

						$this->plugin->getCache()->set($plugin->data_title, $plugin);

						return $plugin;
					}, json_decode($result, true)["hits"]["hits"]);
				}
				call_user_func_array($this->callbacks[$taskId], $plugins ? $plugins : []);
				unset($this->callbacks[$taskId]);
			})
		);
		$this->callbacks[$task->getTaskId()] = $callback;
	}

	public function regex() : string {
		return "forums.pocketmine.net\\/plugins\\/[\\w-]+\\.(\\d+)"; // group 1: plugin ID
	}
}