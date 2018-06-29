<?php
/**
 * Copyright (c) 2017, 2018 KnownUnown
 *
 * This file is part of Sheep.
 *
 * Sheep is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Sheep is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Sheep {

	use Sheep\Command\Command;
	use Sheep\Command\CommandManager;
	use Sheep\Command\HelpCommand;
	use Sheep\Command\Problem\CLIProblem;
	use Sheep\Store\MemoryStore;

	if(!strpos(__FILE__, ".phar")) {
		echo "!!!\nIt is not recommended to run Sheep from source.\n";
		echo "You can get a packaged release of Sheep at https://poggit.pmmp.io/p/Sheep/\n!!!\n";
	}
	require(getVendorPath() . "/vendor/autoload.php");
	define("Sheep\\PLUGIN_PATH", getcwd());
	define("Sheep\\VARIANT", Variant::CONSOLE);

	$store = new MemoryStore();
	Sheep::getInstance()->init(new Async\CLIAsyncHandler(), $store);
	$commandManager = new CommandManager();
	$commandManager->register(new HelpCommand($commandManager->getAll()));

	$command = @$argv[1] && $commandManager->get($argv[1]) instanceof Command
		? $commandManager->get($argv[1]) : $commandManager->get("help");
	$command->run(new CLIProblem(), array_slice($argv, 2));

	foreach($store->getAll() as $plugin) {
		if($plugin["state"] !== PluginState::STATE_NOT_INSTALLED) {
			continue;
		}
		@unlink(\Sheep\PLUGIN_PATH . $plugin["name"] . ".phar");
		$store->remove($plugin["name"]);
	}

	function getVendorPath(): string {
		return strpos(__FILE__, ".phar") ? \Phar::running() : __DIR__;
	}
}

__HALT_COMPILER();
