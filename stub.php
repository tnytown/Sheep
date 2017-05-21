<?php
declare(strict_types=1);

namespace Sheep {
	require "vendor/autoload.php";

	define("Sheep\\PLUGIN_PATH", getcwd());
	Sheep::getInstance()->init(new Async\CLIAsyncHandler());
	$commandManager = new Command\CommandManager();

	if($command = $commandManager->get(@$argv[1])) {
		$command->run(new Command\Problem\CLIProblem(), array_slice($argv, 2));
	}
}

__HALT_COMPILER();