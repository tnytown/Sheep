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

class WriteTask extends AsyncTask {
	use AsyncMixin;

	private $location, $contents;

	public function __construct(string $location, string $contents, callable $callback) {
		$this->storeLocal($callback);

		$this->location = $location;
		$this->contents = $contents;
	}

	public function onRun() {
		$result = $this->writeFile($this->location, $this->contents);
		if(is_int($result)) {
			$result = true;
		} // file_put_contents...
		$this->setResult($result);
	}

	public function onCompletion(Server $server) {
		($this->fetchLocal())($this->getResult());
	}
}
