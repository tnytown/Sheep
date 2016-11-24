<?php


namespace Sheep\Source;


use Sheep\Exception\InvalidPluginException;
use Sheep\Exception\PluginAlreadyInstalledException;
use Sheep\Plugin;
use Sheep\Sheep;
use Sheep\Task\FileGetTask;
use Sheep\Task\FileWriteTask;

abstract class BaseSource implements Source {
	protected $plugin;

	public function __construct(Sheep $plugin) {
		$this->plugin = $plugin;
	}

	public function install(Plugin $plugin, callable $callback) {
		if(!isset($plugin->uri)) {
			throw new InvalidPluginException("Invalid Plugin URI");
		}

		if($plugin->isInstalled()) {
			throw new PluginAlreadyInstalledException;
		}

		$scheduler = $this->plugin->getServer()->getScheduler();

		$scheduler->scheduleAsyncTask(
			$read = new FileGetTask($plugin->uri, function($taskId, $result) use ($scheduler, $callback, $plugin) {
				if($result) {
					$scheduler->scheduleAsyncTask(
						$write = new FileWriteTask(\pocketmine\PLUGIN_PATH . DIRECTORY_SEPARATOR . $plugin->getName(), $result,
							function($taskId, $path) use ($plugin) {
								$plugin->loadDescription(
									$this->plugin->getServer()->getPluginManager()->loadPlugin($path)->getDescription()
								);
							})
					);
				}
			}, $this)
		);
	}

	public function update(Plugin $plugin, callable $callback) {

	}
}