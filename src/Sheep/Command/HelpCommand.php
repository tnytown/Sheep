<?php
declare(strict_types=1);


namespace Sheep\Command;


use Sheep\Command\Problem\Problem;

class HelpCommand extends Command {

	const TAB = "	";
	private $commands;

	public function __construct(array $commands) {
		parent::__construct("help", "Provides help.");

		$this->commands = $commands;
		$this->arg("command", "The command.", false);
	}

	protected function execute(Problem $problem, array $args) {
		if (@$args["command"]) {
			$cmd = @$this->commands[$args["command"]];
			if ($cmd instanceof Command) {
				$problem->print("Usage: " . $cmd->getUsage());
			}
		} else {
			$problem->print("Sheep is a plugin manager for PocketMine-MP.\nAvailable commands:");
			foreach ($this->commands as $command) {
				if ($command instanceof Command) {
					$problem->print($command->getName() . self::TAB . $command->getDescription());
				}
			}
		}
	}
}