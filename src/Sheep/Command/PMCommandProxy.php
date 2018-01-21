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

use pocketmine\command\Command as PMCommand;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use Sheep\Command\Problem\PMProblem;

class PMCommandProxy extends PMCommand {
	private $command;

	public function __construct(Command $command) {
		$this->command = $command;
		parent::__construct($command->getName(), $command->getDescription(), $command->getUsage());
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if(!($sender instanceof ConsoleCommandSender)) {
			$sender->sendMessage("You must execute this command from the console.");
			return;
		}
		$this->command->run(new PMProblem($sender), $args);
	}
}
