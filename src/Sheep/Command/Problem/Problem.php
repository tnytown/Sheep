<?php
declare(strict_types=1);


namespace Sheep\Command\Problem;

/**
 * PEBKAC
 * @package Sheep\Command
 */
interface Problem {

	public function print(string $message);
}