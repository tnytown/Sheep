<?php
declare(strict_types=1);


namespace Sheep\Command;


class CommandManager {
	private $commands;

	public function __construct() {
		$this->registerDefaults();
	}

	public function get(string $command) : Command {
		return @$this->commands[$command];
	}

	/**
	 * @return Command[]
	 */
	public function getAll() : array {
		return $this->commands;
	}

	public function register(Command $command) {
		$this->commands[$command->getName()] = $command;
	}

	public function registerDefaults() {
		$this->register(new InstallCommand());
		$this->register(new UninstallCommand());
		$this->register(new UpdateCommand());
	}
}