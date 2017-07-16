<?php
declare(strict_types=1);


namespace Sheep\Command;


use pocketmine\utils\TextFormat;
use Sheep\Command\Problem\Problem;
use Sheep\Plugin;
use Sheep\PluginState;
use Sheep\Utils\Error;

class InfoCommand extends Command {

	public function __construct() {
		parent::__construct("info", "Displays information about a plugin.");
		$this->arg("plugin", "The plugin in question.", true);
	}

	protected function execute(Problem $problem, array $args) {
		$this->api->info($args["plugin"], "latest")
			->then(function (Plugin $plugin) use (&$problem) {
				$problem->print("- {$plugin->getName()} -");
				foreach ($plugin->getInfo() as $key => $value) {
					switch ($key) {
						case "status":
							$problem->print(TextFormat::GOLD . $key . TextFormat::RESET . ": " . PluginState::STATE_DESC[$value]);
							break;
						case "authors":
							$problem->print(TextFormat::GOLD . $key . TextFormat::RESET . ": " . implode(",", $value));
							break;
						default:
							$problem->print(TextFormat::GOLD . $key . TextFormat::RESET . ": " . $value);
					}
				}
			})
			->otherwise(function (Error $error) use (&$problem) {
				$problem->print("An error occurred: $error");
			});
	}
}