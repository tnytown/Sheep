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
			$task = new FileGetTask(Forums::SEARCH_ENDPOINT . "/search?q=" . $plugin, function($taskId, $result) {
				$plugins = false;
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

						return $plugin;

					}, json_decode($result, true)["hits"]["hits"]);
				}
				call_user_func_array($this->callbacks[$taskId], $plugins ? $plugins : [false]);
				unset($this->callbacks[$taskId]);
			})
		);
		$this->callbacks[$task->getTaskId()] = $callback;
	}

	public function resolve(string $plugin, callable $callback, $exact = true) {
		$this->search($plugin, function(...$plugins) use ($plugin, $callback) {
			$result = false;
			foreach($plugins as $p) {
				if($p->data_outdated) continue;

				if(strtolower($p->data_title) === strtolower($plugin)) {
					$result = $p;
					break;
				}
			}
			call_user_func($callback, $result);
		});
	}
}