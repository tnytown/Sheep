<?php
declare(strict_types = 1);


namespace Sheep;


use Sheep\Async\PMAsyncHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Sheep\Command\CommandManager;
use Sheep\Command\PMCommandProxy;
use Sheep\Source\SourceManager;
use Sheep\Store\FileStore;
use Sheep\Store\Store;

class SheepPlugin extends PluginBase {
	/** @var Sheep */
	private $api;
	/** @var Config */
	private $cache;
	/** @var SourceManager */
	private $sourceManager;
	/** @var CommandManager */
	private $commandManager;

	public function onEnable() {
		include_once("../../vendor/autoload.php");
		define("Sheep\\PLUGIN_PATH", constant("pocketmine\\PLUGIN_PATH"));

		$asyncHandler = new PMAsyncHandler($this->getServer()->getScheduler());
		$this->api = Sheep::getInstance();
		$this->api->init($asyncHandler, $store = new FileStore("sheep.lock"));
		$this->sourceManager = $this->api->getSourceManager();
		$this->commandManager = new CommandManager();

		foreach($this->commandManager->getAll() as $command) {
			$this->getServer()->getCommandMap()->register("sheep", new PMCommandProxy($command));
		}

		register_shutdown_function(function() use (&$store) {
			foreach($store->getAll() as $plugin) {
				switch($plugin["state"]) {
					case PluginState::STATE_UPDATING:
						$base = \Sheep\PLUGIN_PATH . DIRECTORY_SEPARATOR . $plugin["name"];
						if(file_exists($base . ".phar") && file_exists($base . ".phar.update")) {
							try {
								\Phar::unlinkArchive($base . ".phar");
							} catch(\PharException $exception) {
								echo "Sheep Updater failed for plugin \"{$plugin["name"]}\": {$exception->getMessage()}\n";
								break;
							}
							@rename($base . ".phar.update", $base . ".phar");
						}

						$plugin["state"] = PluginState::STATE_INSTALLED;
						$store->update($plugin);
						break;
					case PluginState::STATE_NOT_INSTALLED:
						@unlink(\Sheep\PLUGIN_PATH . $plugin["name"] . ".phar");
						$store->remove($plugin["name"]);
						break;
				}
			}
			$store->persist();
		});

	}

	public function getSourceManager() {
		return $this->sourceManager;
	}

	public function getGitRevision() {
		$ref = @file_get_contents($this->getFile() . DIRECTORY_SEPARATOR . ".git/HEAD");
		if(!$ref) return "unknown";
		$rev = trim(@file_get_contents($this->getFile() . ".git/" . trim(explode(" ", $ref)[1])));
		return $rev ?: "unknown";
	}

	public function getCache() {
		return $this->cache;
	}
}