<?php


namespace Sheep\Command;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use Sheep\Sheep;

class SheepCommand extends Command {
	protected $plugin;

	public function __construct(Sheep $plugin, $name = "sheep", $description = "Command for the Sheep plugin", $usageMessage = "sheep", $aliases = []) {
		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, $commandLabel, array $args) {
		$sender->sendMessage("Sheep version {$this->plugin->getDescription()->getVersion()} (Git commit {$this->plugin->getGitRevision()})");
	}
}