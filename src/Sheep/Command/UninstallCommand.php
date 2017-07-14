<?php


namespace Sheep\Command;


use Sheep\Command\Problem\Problem;
use Sheep\Utils\Error;

class UninstallCommand extends Command {
	public function __construct() {
		parent::__construct("uninstall", "Uninstalls a plugin.");
		$this->arg("plugin", "The plugin to uninstall.", true);
	}

	public function execute(Problem $problem, array $args) {
		$problem->print("Uninstalling plugin {$args["plugin"]}...");
		$this->api->uninstall($args["plugin"])
			->then(function() use (&$problem) {
				$problem->print("Success!");
			})
			->otherwise(function(Error $error) use (&$problem) {
				$problem->print("Failure: $error");
			});
	}
}