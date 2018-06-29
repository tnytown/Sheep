<?php
/**
 * Copyright (c) 2017, 2018 KnownUnown
 *
 * This file is part of Sheep.
 *
 * Sheep is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Sheep is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);


namespace Sheep;


use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Sheep\Async\PMAsyncHandler;
use Sheep\Async\Task\CurlTask;
use Sheep\Command\CommandManager;
use Sheep\Command\InfoCommand;
use Sheep\Command\PMCommandProxy;
use Sheep\Command\UpdateCommand;
use Sheep\Source\SourceManager;
use Sheep\Store\FileStore;
use Sheep\Store\Store;
use Sheep\Updater\UpdateHandler;
use Sheep\Updater\UpdaterTask;
use Sheep\Utils\ScanTask;

class SheepPlugin extends PluginBase {
	/** @var Sheep */
	private $api;
	/** @var Store */
	private $store;
	/** @var SourceManager */
	private $sourceManager;
	/** @var CommandManager */
	private $commandManager;
	/** @var UpdateHandler */
	private $updateHandler;

	public function onEnable() {
		if(!$this->isPhar()) {
			$this->getLogger()->alert("It is not recommended to run Sheep from source.");
			$this->getLogger()->alert("You can get a packaged release of Sheep at https://poggit.pmmp.io/p/Sheep/");
		}

		include_once($this->getFile() . "/vendor/autoload.php");
		self::defineOnce("Sheep\\PLUGIN_PATH", constant("pocketmine\\PLUGIN_PATH"));

		self::defineOnce("Sheep\\VARIANT",
			$this->getServer()->getName() === "PocketMine-MP" ? Variant::POCKETMINE : Variant::SPOON);


		$this->saveDefaultConfig();
		$this->getConfig()->setDefaults((new Config($this->getFile() . "/resources/config.yml", Config::YAML))->getAll());
		$this->getConfig()->save();

		$this->api = Sheep::getInstance();
		$asyncHandler = new PMAsyncHandler($this->getServer()->getAsyncPool()); // TODO: use own AsyncPool
		CurlTask::setMetadata($this->getServer()->getName(), $this->getServer()->getPocketMineVersion());
		$this->api->init($asyncHandler, $store = new FileStore("sheep.lock"));
		$this->store = $store;
		$this->sourceManager = $this->api->getSourceManager();

		$this->updateHandler = new UpdateHandler($this->getServer(),
			$this, $this->api, $this->store, $this->getConfig()->get("updater"));

		$interval = $this->getConfig()->getNested("updater.interval") * UpdateHandler::MINUTES_TO_TICKS;
		if($interval > 0)
			$this->getScheduler()
				->scheduleRepeatingTask(new UpdaterTask($this->updateHandler, $this->api, $this->store), $interval);


		$this->initCommands();
		$this->scan();

		if(!defined("Sheep\\STARTED_UP")) $this->registerUpdater();
		self::defineOnce("Sheep\\STARTED_UP", true);
	}

	private static function defineOnce(string $name, $value) {
		if(!defined($name)) {
			define($name, $value);
		}
	}

	private function initCommands() {
		$this->commandManager = new CommandManager();
		$this->commandManager->register(new UpdateCommand($this->updateHandler));
		$this->commandManager->register(new InfoCommand());

		foreach($this->commandManager->getAll() as $command) {
			$this->getServer()->getCommandMap()->register("sheep", new PMCommandProxy($command));
		}
	}

	private function registerUpdater() {
		$store = $this->store;
		register_shutdown_function(function() use (&$store) {
			$count = 0;
			printf("[*] Sheep Updater is running...\n");
			foreach($store->getAll() as $plugin) {
				switch($plugin["state"]) {
					case PluginState::STATE_UPDATING:
						$base = \Sheep\PLUGIN_PATH . DIRECTORY_SEPARATOR . $plugin["name"];
						if(file_exists($base . ".phar") && file_exists($base . ".phar.update")) {
							try {
								\Phar::unlinkArchive($base . ".phar");
							} catch(\PharException $exception) {
								printf("[!] Sheep Updater failed for plugin \"%s\": %s\n",
									$plugin["name"], $exception->getMessage());
								break;
							}
							@rename($base . ".phar.update", $base . ".phar");
							$count++;
						}

						$plugin["version"] = $plugin["update"];
						$plugin["update"] = null;
						$plugin["state"] = PluginState::STATE_INSTALLED;
						$store->update(Plugin::fromArray($plugin));
						break;
					case PluginState::STATE_NOT_INSTALLED:
						@unlink(\Sheep\PLUGIN_PATH . $plugin["name"] . ".phar");
						$store->remove($plugin["name"]);
						break;
				}
			}

			printf("[*] %d update(s) applied.\n", $count);
			$store->persist();
		});
	}

	private function scan() {
		$this->getLogger()->info("Scanning loaded plugins for changes...");
		$this->getScheduler()->scheduleTask(new ScanTask($this->store));
	}

	public function onDisable() {
		$this->getConfig()->save();
	}

	public function getSourceManager(): SourceManager {
		return $this->sourceManager;
	}

	public function getStore(): Store {
		return $this->store;
	}
}
