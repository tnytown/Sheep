<?php


namespace Sheep\Command;


use pocketmine\command\CommandSender;
use Sheep\Sheep;

class SearchCommand extends SheepCommand {

	public function __construct(Sheep $plugin) {
		parent::__construct($plugin, "search", "Searches for a plugin.", "search <name>[@source]");
	}

	public function run(CommandSender $sender, $commandLabel, array $args) {
		if(!isset($args[0])) {
			return false;
		}

		$info = explode($args[0], "@");
		$this->plugin->search($info[0], function() {

		}, isset($info[1]) ? $info[1] : $this->plugin->getSourceManager()->getDefaultSource());
	}
}