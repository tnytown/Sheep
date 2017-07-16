<?php
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

	protected function arg(string $name, string $description = "", bool $required = false) {
		$this->args[$name] = [$name, $description, $required];
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

	public function getUsage(): string {
		$str = $this->name;
		foreach ($this->args as $arg) {
			if ($arg[2]) { // required
				$str .= " <{$arg[0]}>";
			} else {
				$str .= " [{$arg[0]}]";
			}
		}
		return $str;
	}

	public function run(Problem $problem, array $args) {
		$output = $this->args;
		$keys = array_keys($output);

		$num = count($output);
		for ($i = 0; $i < $num; $i++) {
			if ($arg = @$args[$i]) {
				$output[$keys[$i]] = $arg;
			} else {
				if ($output[$keys[$i]][2]) {
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

	protected abstract function execute(Problem $problem, array $args);
}