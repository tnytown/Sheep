<?php


namespace KnownUnown\Sheep\Command;


use KnownUnown\Sheep\Plugin;
use KnownUnown\Sheep\Sheep;
use KnownUnown\Sheep\Source\Forums;
use KnownUnown\Sheep\Task\FileGetTask;
use KnownUnown\Sheep\Task\FileWriteTask;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PharPluginLoader;
use pocketmine\plugin\PluginDescription;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class SheepCommand extends Command {

	private $plugin;

	public function __construct(Sheep $plugin) {
		$this->plugin = $plugin;

		parent::__construct("sheep", "Base command for the Sheep plugin", "/sheep <install|uninstall|find|about>");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args) {
		if(!$this->testPermission($sender)) {
			return false;
		}

		switch($sub = array_shift($args)) {
			case "install":
				$this->plugin->installPlugin($this->plugin->getSource(Forums::class), implode(" ", $args), function($plugin) use ($sender) {
					if($plugin instanceof PluginDescription) {
						$sender->sendMessage("Successfully installed plugin " . $plugin->getFullName());
					}
				}, $sender);
				break;
			case "uninstall":
				break;
			case "find":
				/** @var Forums $repo */
				$repo = $this->plugin->getSource(Forums::class);

				$repo->search($name = implode(" ", $args), function(...$plugins) use ($name, $sender) {
					if($plugins[0] !== false) {
						$sender->sendMessage("Results for search \"$name\" (" .  count($plugins) . " hits):");
						/** @var Plugin $plugin */
						foreach($plugins as $plugin) {
							if($plugin instanceof Plugin) {
								$sender->sendMessage(
									"==== Plugin $plugin->data_title ===\n" .
									"Description: $plugin->data_description\n" .
									"Author: $plugin->data_author\n" .
									"Rating: $plugin->data_rating / 5\n"
								);

								if($plugin->data_outdated) $sender->sendMessage(
									"This plugin is " . TextFormat::RED . "OUTDATED. " . TextFormat::RESET
									. "Sheep will not (and cannot) install this plugin."
								);
							}
						}
						return;
					}
					$sender->sendMessage(TextFormat::RED . "An unknown error occurred while processing your request.");
				});
				break;
			default:
				$sender->sendMessage($this->getUsage());
		}
		return true;
	}
}