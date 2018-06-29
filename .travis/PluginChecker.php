<?php
declare(strict_types=1);
/**
 * @name PluginChecker
 * @author PEMapModder,KnownUnown
 * @version 1.0.0
 * @api 3.0.0
 * @main PluginChecker\PluginChecker
 */
namespace PluginChecker;
use pocketmine\Server;

class PluginChecker extends \pocketmine\plugin\PluginBase {
	public function onEnable() {
		$this->getScheduler()->scheduleDelayedTask(new CheckTask(), 1);
	}
}
class CheckTask extends \pocketmine\scheduler\Task {
	public function onRun(int $t){
		$server = Server::getInstance();
		$plugin = $server->getPluginManager()->getPlugin(
			$server->getProperty("pluginchecker.target"));

		if($plugin !== null && $plugin->isEnabled()) $server->shutdown();
		else exit(1);
	}
}
