<?php


namespace Sheep\Exception;


class PluginAlreadyInstalledException extends SheepException {
	public function __construct($message = "Plugin is already installed", $code = 0, \Exception $previous = 0) {
		parent::__construct($message, $code, $previous);
	}
}