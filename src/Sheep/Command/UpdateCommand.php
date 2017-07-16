<?php
declare(strict_types=1);


namespace Sheep\Command;


use Sheep\Command\Problem\Problem;
use Sheep\Utils\Error;

class UpdateCommand extends Command {

	public function __construct() {
		parent::__construct("update", "Updates a plugin.");
		$this->arg("plugin", "The plugin to update.", true);
	}

	protected function execute(Problem $problem, array $args) {
		$problem->print("Updating {$args["plugin"]}...");
		$this->api->update($args["plugin"])
			->then(function () use (&$problem) {
				$problem->print("Success!");
			})
			->otherwise(function (Error $error) use (&$problem) {
				$problem->print($error->__toString());
			});
	}
}