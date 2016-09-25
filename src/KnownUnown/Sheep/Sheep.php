<?php


namespace KnownUnown\Sheep;


use KnownUnown\Sheep\Command\SheepCommand;
use KnownUnown\Sheep\Source\Forums;
use KnownUnown\Sheep\Source\Source;
use KnownUnown\Sheep\Task\FileGetTask;
use KnownUnown\Sheep\Task\FileWriteTask;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PharPluginLoader;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginDescription;
use pocketmine\utils\TextFormat;

class Sheep extends PluginBase {

	/** @var Source[] */
	private $sources = [];

	public function onEnable() {
		$this->registerSources();
		$this->getServer()->getCommandMap()->register("sheep", new SheepCommand($this));
		$this->getLogger()->info("Sheep enabled!");

	}

	public function registerSources() {
		$this->sources = [
			Forums::class => new Forums(),
		];
	}

	public function getSource(string $name) {
		if(!isset($this->sources[$name])) {
			return null;
		}
		return $this->sources[$name];
	}

	public function installPlugin(Source $source, string $identifier, callable $callback, CommandSender $sender = null) { // TODO: fix this mess
		$source->resolve($identifier, function($plugin) use ($identifier, &$source, &$callback, &$sender) {
			if($plugin instanceof Plugin) {
				$this->getServer()->getScheduler()->scheduleAsyncTask(
					new FileGetTask($plugin->uri, function($taskId, $plugin) use ($identifier, &$source, &$callback, &$sender) {
						if($plugin) {
							$this->getServer()->getScheduler()->scheduleAsyncTask(
								new FileWriteTask(\pocketmine\PLUGIN_PATH . $identifier . ".phar", $plugin,
									function($taskId, $path) use ($identifier, &$source, &$callback, &$sender) {
										$desc = (new PharPluginLoader($this->getServer()))->getPluginDescription($path); // if null then some serious stuff is wrong
										@rename($path, $path = (\pocketmine\PLUGIN_PATH . $desc->getName() . ".phar")); // normalize, see: "SIRI MASSIVE UPDATE 4.0!!!oneone11"

										$installed = [];
										foreach(($deps = $desc->getDepend()) as $dependency) {
											$this->installPlugin($source, $dependency, function($plugin) use (&$installed, $deps, &$sender, &$callback) {
												$installed[] .= ($plugin instanceof PluginDescription) ? $plugin->getName() : null;
												if(count($installed) === count($deps)) {
													if(($diff = (array_diff($installed, $deps))) !== []) {
														foreach($installed as $plugin) {
															if($plugin instanceof PluginDescription) {
																@unlink(\pocketmine\PLUGIN_PATH . $plugin->getName() . ".phar");
															}
														}
														$plugin = null;
														if(!is_null($sender)) {
															$sender->sendMessage(TextFormat::RED . "Unable to install requested plugin due to unresolvable dependencies:" . $diff);
														}
													}
													call_user_func($callback, $plugin);
												}
											});
										}
										if(count($deps) === 0) {
											$this->getServer()->getPluginManager()->loadPlugin($path);
											call_user_func($callback, $desc);
										}
									})
							);
						}
					})
				);
			}
		});
	}

	public function onDisable() {
	}
}