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


use Sheep\Plugin;

class PoggitPlugin extends Plugin {

	public function __construct(array $data) {
		$this->source = "Poggit";
		$this->name = $data["name"];
		$this->version = $data["version"];
		$this->uri = $data["artifact_url"];
		$this->dependencies = $data["deps"];
		$authors = [explode("/", $data["repo_name"])[0]];

		$this->info = [
			"source" => $this->source,
			"name" => $this->name,
			"version" => $this->version,
			"authors" => $authors
		];
	}
}
