<?php
declare(strict_types=1);


namespace Sheep\Source;

use Throwable;

class PluginNotFoundException extends \Exception {
	public function __construct(string $plugin) {
		parent::__construct("Plugin {$plugin} not found", 0, null);
	}
}
