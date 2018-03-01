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


namespace Sheep\Async\Task;


use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use Sheep\Async\AsyncMixin;

class CurlTask extends AsyncTask {
	use AsyncMixin;

	private $url;
	private $type;
	private $timeout;

	private $args;
	private $headers;

	private $ver, $var;

	public function __construct(
		string $url,
		int $type,
		callable $callback,
		int $timeout = 10,
		array $headers = [],
		array $args = [],
		string $version,
		int $variant
	) {
		$this->storeLocal($callback);

		$this->url = $url;
		$this->type = $type;
		$this->timeout = $timeout;
		$this->headers = (array) $headers;
		$this->args = (array) $args;

		$this->ver = $version;
		$this->var = $variant;
	}

	public function onRun() {
		if(!defined("Sheep\\VERSION")) { // constants may already be defined in this worker
			define("Sheep\\VERSION", $this->ver); // hack because pthreads doesn't copy over constants (for docURL in mixin)
			define("Sheep\\VARIANT", $this->var);
		}
		$error = null;
		$result = $this->docURL($this->url, $this->type, $this->timeout, $this->headers, $this->args, $error);
		$this->setResult([$result, $error]);
	}

	public function onCompletion(Server $server) {
		($this->fetchLocal())(...$this->getResult());
	}
}
