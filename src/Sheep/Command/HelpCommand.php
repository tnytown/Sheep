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


use Sheep\Command\Problem\Problem;

class HelpCommand extends Command {

	private $commands;

	public function __construct(array $commands) {
		parent::__construct("help", "Provides help.");

		$this->commands = $commands;
		$this->arg("command", "The command.", false);
	}

	protected function execute(Problem $problem, array $args) {
		if(@$args["command"]) {
			$cmd = @$this->commands[$args["command"]];
			if($cmd instanceof Command) {
				$problem->print("Usage: " . $cmd->getUsage());
			}
		} else {
			$problem->print("Sheep is a plugin manager for PocketMine-MP.\nAvailable commands:");
			foreach($this->commands as $command) {
				if($command instanceof Command) {
					$problem->print($command->getName() . "\t" . $command->getDescription());
				}
			}
		}
	}
}
