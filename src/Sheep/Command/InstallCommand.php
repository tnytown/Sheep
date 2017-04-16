<?php


namespace Sheep\Command;

use pocketmine\command\CommandSender;
use Sheep\Plugin;
use Sheep\Sheep;
use Sheep\Source\Source;
use Sheep\Utils\Error;

class InstallCommand extends SheepCommand {

	public function __construct(Sheep $plugin) {
		parent::__construct($plugin, "install", "Installs a plugin.", "install [@source/]<plugin>[:version]");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args) {
		// parsing data
		preg_match("/(?:@(\\w+)\\/)?(\\w+)(?::?(.+))?/", $args[0], $stuff); // @source/plugin:version
		array_shift($stuff);

		$source = $stuff[0] === "" ? $this->plugin->getSourceManager()->getDefaultSource()
										: $this->plugin->getSourceManager()->get($stuff[0]);
		if(!($source instanceof Source)) {
			return $sender->sendMessage("Invalid source supplied.");
		}
		$name = $stuff[1];
		$version = isset($stuff[2]) ? $stuff[2] : "latest";

		// searching for the plugin
		$source->resolve($name, $version, function(Error $error = null, $results) use ($source, $sender) {
			if($error) {
				return $sender->sendMessage("An error occurred: " . $error);
			}
			if(($num = count($results)) !== 1) {
				return $sender->sendMessage("($num) results found.");
			} else {
				/** @var Plugin $plugin */
				$plugin = $results[0];
				$source->install($plugin, function(Error $error = null) use ($plugin, $sender) {
					if($error) {
						return $sender->sendMessage("Error occurred during installation: " . $error);
					}
					$sender->sendMessage("Install success!");
				});
			}
		});
	}
}