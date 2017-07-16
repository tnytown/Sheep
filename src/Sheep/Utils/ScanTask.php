<?php
declare(strict_types=1);


namespace Sheep\Utils;


use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use Sheep\PluginState;
use Sheep\Sheep;
use Sheep\Store\Store;

class ScanTask extends PluginTask {
	private $store;

	public function __construct(Plugin $owner, Store $store) {
		parent::__construct($owner);
		$this->store = $store;
	}

	public function onRun($currentTick) {
		$server = $this->getOwner()->getServer();
		$sheep = Sheep::getInstance();

		foreach ($server->getPluginManager()->getPlugins() as $plugin) {
			if ($this->store->exists($plugin->getName())) {
				continue;
			}

			$sheep->info($plugin->getName(), $plugin->getDescription()->getVersion())// search default source
			->then(function (\Sheep\Plugin $plugin) {
				$plugin->setState(PluginState::STATE_INSTALLED);
				$this->store->add($plugin);
			})
				->otherwise(function (Error $error) use (&$plugin) {
					$this->store->add(\Sheep\Plugin::fromArray([
						"source" => "Local",
						"name" => $plugin->getName(),
						"version" => $plugin->getDescription()->getVersion(),
						"state" => PluginState::STATE_INSTALLED
					]));
				});
		}
	}
}