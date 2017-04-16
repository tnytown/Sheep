<?php


namespace Sheep\Source;


use pocketmine\plugin\PluginManager;
use Sheep\Plugin;
use Sheep\Utils\Utils;
use Sheep\Utils\Error;

abstract class BaseSource implements Source {
	protected $pluginManager;

	public function __construct(PluginManager $pluginManager) {
		$this->pluginManager = $pluginManager;
	}

	public function install(Plugin $plugin, callable $callback, array $args = []) {
		if($plugin->getUri() === null) { // TODO: more robust URI checking
			$callback(new Error("Plugin URI is invalid.", Error::E_PLUGIN_URI_INVALID));
		}

		if($plugin->isInstalled()) {
			$callback(new Error("Plugin {$plugin->getName()} is already installed.",
				Error::E_PLUGIN_ALREADY_INSTALLED));
		}

		// I am deeply sorry for the crimes I have committed against
		// closures (and humanity) and I promise to never do this again.

		// Basic structure:
		// install(plugin, callback_1, args)
		// 	-> installDeps(deps_1, install_plugin)
		//		-> install(dep_1, callback(installDeps with deps_1, callback_1)
		//		-> ...
		//		-> install(dep_n, callback(installDeps with deps_1, callback_1)
		//		(deps_1 > 0) === false)
		//			-> install_plugin(null)
		//				-> download_and_install_plugin($plugin)
		//				-> callback_1(args)

		$deps = $plugin->getDependencies();
		$this->installDependencies($deps, function(Error $error = null) use ($plugin, $callback, $args) {
			if(!$error) {
				Utils::curlGet($plugin->getUri(), function(string $result) use ($plugin, $callback, $args) {
					if($result) {
						Utils::writeFile(\pocketmine\PLUGIN_PATH . DIRECTORY_SEPARATOR . $plugin->getName() . ".phar", $result,
							function($path) use ($plugin, $callback, $args) {
								$this->pluginManager->loadPlugin($path);
								$callback(...$args);
							}
						);
					}
				}
				);
			} else {
				$callback($error);
			}
		});
	}

	private function installDependencies(array &$deps, callable $callback) {
		if(count($deps) > 0) {
			$dep = array_shift($deps);
			if(!$dep["isHard"]) return $this->installDependencies($deps, $callback); // skip non-hard
			$this->resolve($dep["name"], $dep["version"], function(Error $error = null, array $results) use (&$deps, $dep, $callback) {
				if($error) {
					$callback($error);
				} else if(count($results) > 1) {
					$callback(new Error("{$dep["name"]}@{$dep["version"]} has multiple install candidates.", Error::E_PLUGIN_MULTIPLE_CANDIDATES));
				} else if(count($results) == 0) {
					$callback(new Error("{$dep["name"]}@{$dep["version"]} (dependency) has no install candidates.", Error::E_PLUGIN_NO_CANDIDATES));
				} else {
					$this->install($results[0], $dep["version"], [$this, "installDependencies"], [$deps, $callback]);
				}
			});
		} else {
			$callback(null);
		}
	}

	public function update(Plugin $plugin, callable $callback) {

	}
}