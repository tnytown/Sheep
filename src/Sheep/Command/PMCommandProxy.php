<?php


namespace Sheep\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use Sheep\Command\Problem\PMProblem;

class PMCommandProxy extends Command {
	private $command;

	public function __construct(\Sheep\Command\Command $command) {
		$this->command = $command;
		parent::__construct($command->getName(), $command->getDescription(), $command->getUsage());
	}

	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!($sender instanceof ConsoleCommandSender)) {
			$sender->sendMessage("You must execute this command from the console.");
			return;
		}
		$this->command->run(new PMProblem($sender), $args);
	}
}