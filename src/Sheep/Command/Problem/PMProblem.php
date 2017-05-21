<?php
declare(strict_types=1);


namespace Sheep\Command\Problem;


use pocketmine\command\ConsoleCommandSender;

class PMProblem implements Problem {
	private $sender;

	public function __construct(ConsoleCommandSender $sender) {
		$this->sender = $sender;
	}

	public function print(string $message) {
		$this->sender->sendMessage($message);
	}
}