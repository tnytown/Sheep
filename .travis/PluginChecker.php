<?php
/**
 * @name PluginChecker
 * @author PEMapModder,KnownUnown
 * @version 1.0.0
 * @api 3.0.0-ALPHA11
 * @main PluginChecker\PluginChecker
 */
namespace PluginChecker;
class PluginChecker extends \pocketmine\plugin\PluginBase {
	public function onEnable() {
		$this->getServer()->getScheduler()->scheduleDelayedTask(new CheckTask($this), 1);
	}
}
class CheckTask extends \pocketmine\scheduler\PluginTask {
	public function onRun(int $t){
		$plugin = $this->owner->getServer()->getPluginManager()->getPlugin(
			$this->owner->getServer()->getProperty("pluginchecker.target"));

		if($plugin !== null && $plugin->isEnabled()) $this->owner->getServer()->shutdown();
		else exit(1);
	}
}
