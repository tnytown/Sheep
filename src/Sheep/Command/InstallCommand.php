<?php
declare(strict_types=1);


namespace Sheep\Command;


use pocketmine\Server;
use Sheep\Command\Problem\PMProblem;
use Sheep\Command\Problem\Problem;
use Sheep\Utils\Error;

class InstallCommand extends Command {

	public function __construct() {
		parent::__construct("install", "Installs a plugin.");
		$this->arg("plugin", "The plugin to install.", true);
		$this->arg("version", "The version of the plugin.");
	}

	public function execute(Problem $problem, array $args) {
		$problem->print("Installing plugin {$args["plugin"]}...");
		$this->api->install($plugin = $args["plugin"], @$args["version"] ?: "latest")
			->then(function () use (&$problem, $plugin) {
				$problem->print("Success!");
				if($problem instanceof PMProblem) {
					Server::getInstance()->getPluginManager()
						->loadPlugin(\Sheep\PLUGIN_PATH . DIRECTORY_SEPARATOR . $plugin . ".phar");
				}
			})
			->otherwise(function (Error $error) use (&$problem) {
				$problem->print("Failure: $error");
			});
	}
}
