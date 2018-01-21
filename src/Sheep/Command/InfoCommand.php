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


use pocketmine\utils\TextFormat;
use Sheep\Command\Problem\Problem;
use Sheep\Plugin;
use Sheep\PluginState;
use Sheep\Utils\Error;

class InfoCommand extends Command {

	public function __construct() {
		parent::__construct("info", "Displays information about a plugin.");
		$this->arg("plugin", "The plugin in question.", true);
	}

	protected function execute(Problem $problem, array $args) {
		$this->api->info($args["plugin"], "latest")
			->then(function(Plugin $plugin) use (&$problem) {
				$problem->print("- {$plugin->getName()} -");
				foreach($plugin->getInfo() as $key => $value) {
					switch($key) {
						case "status":
							$problem->print(TextFormat::GOLD . $key . TextFormat::RESET . ": " . PluginState::STATE_DESC[$value]);
							break;
						case "authors":
							$problem->print(TextFormat::GOLD . $key . TextFormat::RESET . ": " . implode(",", $value));
							break;
						default:
							$problem->print(TextFormat::GOLD . $key . TextFormat::RESET . ": " . $value);
					}
				}
			})
			->otherwise(function(Error $error) use (&$problem) {
				$problem->print("An error occurred: $error");
			});
	}
}
