<?php


namespace KnownUnown\Sheep\Source;


use KnownUnown\Sheep\Plugin;

interface Source {

	public function search(string $plugin, callable $callback);

}