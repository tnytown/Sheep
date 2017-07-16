<?php
declare(strict_types=1);


namespace Sheep\Source;


use Sheep\Async\AsyncHandler;

class SourceManager {
	private $asyncHandler;
	/** @var Source[] */
	private $sources;

	public function __construct(AsyncHandler $asyncHandler) {
		$this->asyncHandler = $asyncHandler;
		$this->sources = [];
		$this->registerDefaults();
	}

	public function get(string $name) {
		return isset($this->sources[$name]) ? $this->sources[$name] : false;
	}

	public function register(string $name, Source $source) {
		if (preg_match("/@deprecated/", (new \ReflectionClass($source))->getDocComment()) > 0) {
			trigger_error("The source $name is deprecated. Using this is not a good idea!", E_USER_DEPRECATED);
		}

		$this->sources[$name] = $source;
	}

	public function registerDefaults() {
		$this->register("Poggit", new PoggitSource($this->asyncHandler));
	}

	public function getDefaultSource() {
		return $this->sources["Poggit"];
	}
}