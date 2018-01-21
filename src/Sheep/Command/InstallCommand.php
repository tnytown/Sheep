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
			->then(function() use (&$problem, $plugin) {
				$problem->print("Success!");
				if($problem instanceof PMProblem) {
					Server::getInstance()->getPluginManager()
						->loadPlugin(\Sheep\PLUGIN_PATH . DIRECTORY_SEPARATOR . $plugin . ".phar");
				}
			})
			->otherwise(function(Error $error) use (&$problem) {
				$problem->print("Failure: $error");
			});
	}
}
