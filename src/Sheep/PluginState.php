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


namespace Sheep;


class PluginState {
	const STATE_NOT_INSTALLED = 0;
	const STATE_INSTALLING = 1;
	const STATE_INSTALLING_DEPS = 2;
	const STATE_INSTALLED = 3;
	const STATE_UPDATING = 4;

	const STATE_DESC = [
		self::STATE_NOT_INSTALLED => "not installed",
		self::STATE_INSTALLING => "install",
		self::STATE_INSTALLING_DEPS => "install dependencies",
		self::STATE_INSTALLED => "installed",
		self::STATE_UPDATING => "updating",
	];
}
