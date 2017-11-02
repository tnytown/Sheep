<?php
declare(strict_types=1);

namespace Sheep {

	use Sheep\Command\CommandManager;
	use Sheep\Command\Command;
	use Sheep\Command\HelpCommand;
	use Sheep\Command\Problem\CLIProblem;
	use Sheep\Store\MemoryStore;

	if(!strpos(__FILE__, ".phar")) {
	    echo "!!!\nIt is not recommended to run Sheep from source.\n";
	    echo "You can get a packaged release of Sheep at https://poggit.pmmp.io/p/Sheep/\n!!!\n";
    }
	require(getVendorPath() . "/vendor/autoload.php");
	define("Sheep\\PLUGIN_PATH", getcwd());

	$store = new MemoryStore();
	Sheep::getInstance()->init(new Async\CLIAsyncHandler(), $store);
	$commandManager = new CommandManager();
	$commandManager->register(new HelpCommand($commandManager->getAll()));

	$command = @$argv[1] && $commandManager->get($argv[1]) instanceof Command
		? $commandManager->get($argv[1]) : $commandManager->get("help");
	$command->run(new CLIProblem(), array_slice($argv, 2));

	foreach($store->getAll() as $plugin) {
		if($plugin["state"] !== PluginState::STATE_NOT_INSTALLED) continue;
		@unlink(\Sheep\PLUGIN_PATH . $plugin["name"] . ".phar");
		$store->remove($plugin["name"]);
	}

	function getVendorPath() : string {
		return strpos(__FILE__, ".phar") ? \Phar::running() : __DIR__;
	}
}


__HALT_COMPILER();

