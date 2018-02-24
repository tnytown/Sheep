<?php
/**
 * Copyright (c) 2017, 2018 KnownUnown
 *
 * This file is part of Sheep.
 *
 * Sheep is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Sheep is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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

	public function registerDefaults() {
		$this->register("Poggit", new PoggitSource($this->asyncHandler));
	}

	public function register(string $name, Source $source) {
		if(preg_match("/@deprecated/", (new \ReflectionClass($source))->getDocComment()) > 0) {
			trigger_error("The source $name is deprecated. Using this is not a good idea!", E_USER_DEPRECATED);
		}

		$this->sources[$name] = $source;
	}

	/**
	 * Gets a source.
	 *
	 * @param string $name The name of the source.
	 *
	 * @return Source
	 * @throws SourceNotFoundException
	 */
	public function get(string $name): Source {
		if(!isset($this->sources[$name])) throw new SourceNotFoundException($name);
		return $this->sources[$name];
	}

	public function getDefaultSource(): Source {
		return $this->sources["Poggit"];
	}
}
