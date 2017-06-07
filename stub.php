<?php
declare(strict_types=1);

namespace Sheep {

	use Sheep\Command\CommandManager;
	use Sheep\Command\Command;
	use Sheep\Command\HelpCommand;
	use Sheep\Command\Problem\CLIProblem;
	use Sheep\Utils\Lockfile;

	require("vendor/autoload.php");

	define("Sheep\\PLUGIN_PATH", getcwd());
	Sheep::getInstance()->init(new Async\CLIAsyncHandler(), new Lockfile());
	$commandManager = new CommandManager();
	$commandManager->register(new HelpCommand($commandManager->getAll()));

	$command = @$argv[1] && $commandManager->get($argv[1]) instanceof Command
		? $commandManager->get($argv[1]) : $commandManager->get("help");
	$command->run(new CLIProblem(), array_slice($argv, 2));
}

__HALT_COMPILER();