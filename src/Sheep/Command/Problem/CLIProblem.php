<?php
declare(strict_types=1);


namespace Sheep\Command\Problem;


class CLIProblem implements Problem {
	private $buffer;

	public function __construct($buffer = STDIN) {
		$this->buffer = $buffer;
	}

	public function print(string $message) {
		fwrite($this->buffer, $message);
	}
}