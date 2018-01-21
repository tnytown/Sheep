<?php
declare(strict_types=1);


namespace Sheep;


use pocketmine\plugin\PharPluginLoader;
use pocketmine\plugin\PluginBase;
use Sheep\Async\PMAsyncHandler;
use Sheep\Command\CommandManager;
use Sheep\Command\InfoCommand;
use Sheep\Command\PMCommandProxy;
use Sheep\Command\UpdateCommand;
use Sheep\Source\SourceManager;
use Sheep\Store\FileStore;
use Sheep\Utils\ScanTask;

class SheepPlugin extends PluginBase {
	/** @var Sheep */
	private $api;
	private $store;
	/** @var SourceManager */
	private $sourceManager;
	/** @var CommandManager */
	private $commandManager;

	public function onEnable() {
	    if(!$this->isPhar()) {
	        $this->getLogger()->alert("It is not recommended to run Sheep from source.");
	        $this->getLogger()->alert("You can get a packaged release of Sheep at https://poggit.pmmp.io/p/Sheep/");
        }

		include_once($this->getFile() . "/vendor/autoload.php");
		self::defineOnce("Sheep\\PLUGIN_PATH", constant("pocketmine\\PLUGIN_PATH"));

		$asyncHandler = new PMAsyncHandler($this->getServer()->getScheduler());
		$this->api = Sheep::getInstance();
		$this->api->init($asyncHandler, $store = new FileStore("sheep.lock"));
		$this->store = $store;
		$this->sourceManager = $this->api->getSourceManager();
		$this->commandManager = new CommandManager();
		$this->commandManager->register(new UpdateCommand());
		$this->commandManager->register(new InfoCommand());

		foreach ($this->commandManager->getAll() as $command) {
			$this->getServer()->getCommandMap()->register("sheep", new PMCommandProxy($command));
		}
		$this->scan();

		if(!defined("Sheep\\STARTED_UP"))
		register_shutdown_function(function () use (&$store) {
			echo "[*] Sheep Updater is running...\n";
			foreach ($store->getAll() as $plugin) {
				switch ($plugin["state"]) {
					case PluginState::STATE_UPDATING:
						$base = \Sheep\PLUGIN_PATH . DIRECTORY_SEPARATOR . $plugin["name"];
						if (file_exists($base . ".phar") && file_exists($base . ".phar.update")) {
                            try {
                                \Phar::unlinkArchive($base . ".phar");
                            } catch (\PharException $exception) {
                                echo "[!] Sheep Updater failed for plugin \"{$plugin["name"]}\": {$exception->getMessage()}\n";
                                break;
                            }
                            @rename($base . ".phar.update", $base . ".phar");
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

			$store->persist();
		});
        self::defineOnce("Sheep\\STARTED_UP", true);
	}

	public function scan() {
		$this->getLogger()->info("Scanning loaded plugins for changes...");
		$this->getServer()->getScheduler()->scheduleTask(new ScanTask($this, $this->store));
	}

	public function getSourceManager() {
		return $this->sourceManager;
	}

	private static function defineOnce(string $name, $value) {
	    if(!defined($name)) define($name, $value);
    }

	public function getStore() {
		return $this->store;
	}
}
