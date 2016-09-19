<?php


namespace KnownUnown\Sheep\Source;


use KnownUnown\Sheep\Plugin;
use KnownUnown\Sheep\Task\FileGetTask;
use pocketmine\Server;

class Forums implements Source {

	/** @var callable[] */
	public $callbacks = [];

	const SEARCH_ENDPOINT = "https://pluginsearch.pocketdock.io";
	const FORUMS_ENDPOINT = "https://forums.pocketmine.net/api.php";

	public function search(string $plugin, callable $callback) {
		Server::getInstance()->getScheduler()->scheduleAsyncTask(
			$task = new FileGetTask(Forums::SEARCH_ENDPOINT . "/search?q=" . $plugin, $closure = function($taskId, $result) {
				if($result && isset($this->callbacks[$taskId])) {
					$plugins = array_map(function(array $value) {
						$data = $value["_source"];

						if($data["prefix_id"] !== 7) { // Not an outdated plugin :)
							$plugin = new Plugin($this);
							$plugin->uri = "https://forums.pocketmine.net/plugins/" .
								$data["resource_id"] . "/download?version=" . $data["current_version_id"];

							$plugin->data_title = $data["title"];
							$plugin->data_author = $data["username"];
							$plugin->data_rating = round($data["rating_avg"], 2);
							$plugin->data_description = $data["tag_line"];
							$plugin->data_version_id = $data["current_version_id"];

							return $plugin;
						}

						return false;
					}, json_decode($result, true)["hits"]["hits"]);
					call_user_func_array($this->callbacks[$taskId], $plugins);
					unset($this->callbacks[$taskId]);
				}
			})
		);
		$refl = new \ReflectionFunction($closure);
		$this->callbacks[$task->getTaskId()] = $callback;
	}
}