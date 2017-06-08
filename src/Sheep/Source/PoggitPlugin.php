<?php
declare(strict_types = 1);


namespace Sheep\Source;


use pocketmine\utils\TextFormat as F;
use Sheep\Plugin;

class PoggitPlugin extends Plugin {
	private $data;

	private $page;
	private $poggitState;
	private $color;

	public function __construct(array $data) {
		parent::__construct("Poggit");
		$this->data = $data;

		$this->name = $data["name"];
		$this->version = $data["version"];
		$this->uri = $data["artifact_url"];
		$this->dependencies = $data["deps"];
		$this->authors = [explode("/", $data["repo_name"])[0]]; // TODO: hack
		$this->page = $data["html_url"];
		$this->poggitState = $data["state"];
		//$this->color = $this->stateColor($this->state);
	}

	public function info() : string {
		$authors = implode(", ", $this->authors);
		return "Name: $this->name\nVersion: $this->version\nAuthor: $authors\n
					Page: $this->page\nState: $this->color{$this->data["state_name"]}";
	}
	/*
	private function stateColor(int $state) : string {
		if(0 <= $state && $state <= 2) return F::RED;
		if(3 <= $state && $state <= 4) return F::YELLOW;
		if($state === 5) return F::GREEN;
		if($state === 6) return F::GOLD;
		return F::STRIKETHROUGH;
	}*/
}