<?php


namespace KnownUnown\Sheep\Source;


interface Source {

	public function search(string $plugin, callable $callback);
	public function resolve(string $plugin, callable $callback, $exact = true);

}