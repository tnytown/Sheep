<?php
declare(strict_types = 1);


namespace Sheep\Source;


use React\Promise\Promise;
use Sheep\Plugin;

interface Source {

	/**
	 * Searches for a plugin.
	 *
	 * @param string $query The name of the plugin.
	 * @return Promise
	 */
	public function search(string $query) : Promise;

	/**
	 * Resolves a single version of a plugin.
	 *
	 * @param string $plugin
	 * @param string $version
	 * @return Promise
	 */
	public function resolve(string $plugin, string $version) : Promise;

	/**
	 * Installs a given Plugin. Implement this function to gain custom
	 * functionality or to work around repository quirks, e.g. requiring
	 * a cookie to download a plugin.
	 *
	 * @param Plugin|Plugin[] ...$plugin The plugin(s) in question.
	 * @return Promise
	 */
	public function install(Plugin... $plugin) : Promise;

	/**
	 * Updates a given Plugin. Implement this function to gain custom
	 * functionality or to work around repository quirks.
	 *
	 * @param Plugin $plugin The plugin in question.
	 * @return Promise
	 */
	public function update(Plugin $plugin) : Promise;
}