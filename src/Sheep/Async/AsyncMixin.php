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


namespace Sheep\Async;


use Sheep\Variant;

trait AsyncMixin {
	private static $brand, $version;

	public function docURL(
		string $url,
		int $type,
		$timeout = 10,
		$extraHeaders = [],
		$args = [],
		&$err = null
	): string {
		$ch = curl_init($url);

		if($type === CurlOptions::CURL_POST) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		}

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
			array_merge([sprintf("User-Agent: Sheep/%s (%s; %s %s)",
				\Sheep\VERSION, Variant::VARIANT_DESC[\Sheep\VARIANT], self::$brand, self::$version)],
				$extraHeaders));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, (int)$timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)$timeout);
		$ret = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);

		if(!$ret) {
			$ret = "";
		} // curl_exec returns false on error
		return $ret;
	}

	public function readFile(string $location): string {
		return @file_get_contents($location);
	}

	public function writeFile(string $location, string $contents) {
		return @file_put_contents($location, $contents);
	}

	public static function setMetadata(string $brand, string $version) {
		self::$brand = $brand;
		self::$version = $version;
	}
}
