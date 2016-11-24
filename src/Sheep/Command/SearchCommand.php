<?php


namespace Sheep\Command;


use pocketmine\command\CommandSender;
use Sheep\Sheep;

class SearchCommand extends SheepCommand {

	public function __construct(Sheep $plugin) {
		parent::__construct($plugin, "search", "Searches for a plugin.", "search <name> [source]");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args) {
		$this->plugin->search($args[0], function(){

		}, isset($args[1]) ? $args[1] : "Forums");
	}
}