<?php


namespace Sheep\Command;

use pocketmine\command\CommandSender;
use Sheep\Sheep;

class InstallCommand extends SheepCommand {

	public function __construct(Sheep $plugin) {
		parent::__construct($plugin, "install", "Installs a plugin.", "install <plugin>");
	}

	public function execute(CommandSender $sender, $commandLabel, array $args) {

	}
}