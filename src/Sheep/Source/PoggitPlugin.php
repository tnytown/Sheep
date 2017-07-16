<?php
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