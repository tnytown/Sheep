<?php


namespace KnownUnown\Sheep;


use KnownUnown\Sheep\Command\SheepCommand;
use KnownUnown\Sheep\Source\Forums;
use KnownUnown\Sheep\Source\Source;
use pocketmine\plugin\PluginBase;

class Sheep extends PluginBase {

	/** @var Source[] */
	private $sources = [];

	public function onEnable() {
		$this->registerSources();

		$this->getServer()->getCommandMap()->register("sheep", new SheepCommand($this));
	}

	public function registerSources() {
		$this->sources = [
			Forums::class => new Forums(),
		];
	}

	public function getSource(string $name) {
		if(!isset($this->sources[$name])) {
			return null;
		}
		return $this->sources[$name];
	}

	public function callbackRouter(callable $callback, ...$args) {
		call_user_func_array($callback, $args);
	}

	public function onDisable() {
	}
}