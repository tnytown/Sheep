<?php
declare(strict_types = 1);


namespace Sheep;


class PluginState {
	const STATE_NOT_INSTALLED 	= 0;
	const STATE_INSTALLING 	  	= 1;
	const STATE_INSTALLING_DEPS = 2;
	const STATE_INSTALLED		= 3;

	const STATE_DESC = [
		self::STATE_NOT_INSTALLED 	=> "not installed",
		self::STATE_INSTALLING	  	=> "install",
		self::STATE_INSTALLING_DEPS => "install dependencies",
		self::STATE_INSTALLED		=> "installed"
	];
}