<?php

namespace Sheep\Source;

use Sheep\Exception\SheepException;
use Sheep\Plugin;
use Sheep\Task\FileGetTask;

/**
 * PocketMine Forums (forums.pocketmine.net) source.
 * Don't look at this, it might and will harm your eyes.
 */
class Forums extends BaseSource {

	const SEARCH_ENDPOINT = "https://pluginsearch.pocketdock.io";
	const FORUMS_ENDPOINT = "https://forums.pocketmine.net/api.php";

	public function search(string $plugin, callable $callback) {
		$this->plugin->getServer()->getScheduler()->scheduleAsyncTask(
			$task = new FileGetTask(Forums::SEARCH_ENDPOINT . "/search?q=$plugin", function($taskId, $result) use ($callback) {
				$plugins = null;
				if($result) {
					$plugins = array_map(function(array $value) {
						$data = $value["_source"];

						$plugin = new Plugin($this);

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
				call_user_func_array($callback, $plugins);
			})
		);
	}

	public function resolve(array $data, callable $callback) {
		if(!isset($data[1])) throw new SheepException("Invalid data passed to Forums::resolve!");
		$this->plugin->getServer()->getScheduler()->scheduleAsyncTask(
			$task = new FileGetTask(Forums::FORUMS_ENDPOINT . "?action=getResource&value=$data[1]", function($taskId, $result) {
				$plugin = new Plugin($this);
			})
		);
	}

	private function generateUri($id, $version) {
		return "https://forums.pocketmine.net/plugins/$id/download?version=$version";
	}

	public function regex() : string {
		return "forums.pocketmine.net\\/plugins\\/[\\w-]+\\.(\\d+)"; // group 1: plugin ID
	}
}