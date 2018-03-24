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
use Sheep\Sheep;

abstract class Command {
	protected $api;

	protected $name, $description;
	protected $args;

	public function __construct(string $name, string $description) {
		$this->api = Sheep::getInstance();

		$this->name = $name;
		$this->description = $description;
		$this->args = [];
	}

	public function getName(): string {
		return $this->name;
	}

	public function getDescription(): string {
		return $this->description;
	}

	public function getArguments(): array {
		return $this->args;
	}

	public function run(Problem $problem, array $args) {
		$output = $this->args;
		$keys = array_keys($output);

		$num = count($output);
		for($i = 0; $i < $num; $i++) {
			if($arg = @$args[$i]) {
				$output[$keys[$i]] = $arg;
			} else {
				if($output[$keys[$i]][2]) {
					$problem->print($this->getUsage());
					return;
				} else {
					$output = array_slice($output, 0, $i);
					break;
				}
			}
		}
		$this->execute($problem, $output);
	}

	public function getUsage(): string {
		$str = $this->name;
		foreach($this->args as $arg) {
			$str .= " " . $arg[2] ? " <{$arg[0]}>" : " [{$arg[0]}]"; // required
		}
		return $str;
	}

	protected abstract function execute(Problem $problem, array $args);

	protected function arg(string $name, string $description = "", bool $required = false) {
		$this->args[$name] = [$name, $description, $required];
	}
}
