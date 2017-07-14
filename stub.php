<?php
declare(strict_types=1);

namespace Sheep {

	use Sheep\Command\CommandManager;
	use Sheep\Command\Command;
	use Sheep\Command\HelpCommand;
	use Sheep\Command\Problem\CLIProblem;
	use Sheep\Store\MemoryStore;

	require("vendor/autoload.php");

	define("Sheep\\PLUGIN_PATH", getcwd());
	Sheep::getInstance()->init(new Async\CLIAsyncHandler(), new MemoryStore());
	$commandManager = new CommandManager();
	$commandManager->register(new HelpCommand($commandManager->getAll()));

	$command = @$argv[1] && $commandManager->get($argv[1]) instanceof Command
		? $commandManager->get($argv[1]) : $commandManager->get("help");
	$command->run(new CLIProblem(), array_slice($argv, 2));

	foreach($this->store->getAll() as $plugin) {
		if($plugin["state"] !== PluginState::STATE_NOT_INSTALLED) continue;
		@unlink(\Sheep\PLUGIN_PATH . $plugin["name"] . ".phar");
		$this->store->remove($plugin["name"]);
	}
}

__HALT_COMPILER();