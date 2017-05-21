<?php


namespace Sheep\Command;


use Sheep\Command\Problem\Problem;

class InstallCommand extends Command {

	public function __construct() {
		parent::__construct("install", "Installs a plugin.");
		$this->arg("plugin", "The plugin to install.", true);
		$this->arg("version", "The version of the plugin.");
	}

	public function execute(Problem $problem, array $args) {
		$problem->print("Installing plugin {$args["plugin"]}...");
		$this->api->install($args["plugin"], @$args["version"] ?: "latest")
			->then(function() use (&$problem) {
				$problem->print("Success!");
			})
			->otherwise(function($error) use (&$problem) {
				$problem->print("Failure :(\n");
			});
	}
}