<?php


namespace Sheep\Source;


use Sheep\Plugin;

interface Source {

	/**
	 * Searches for a plugin.
	 *
	 * @param string $query The name of the plugin.
	 * @param callable $callback The callback for receiving data.
	 */
	public function search(string $query, callable $callback);

	/**
	 * Resolves a plugin exactly.
	 *
	 * @param string $plugin
	 * @param callable $callback
	 * @param string $version
	 * @return mixed
	 */
	public function resolve(string $plugin, string $version, callable $callback);

	/**
	 * Installs a given Plugin. Implement this function to gain custom
	 * functionality or to work around repository quirks, e.g. requiring
	 * a cookie to download a plugin.
	 *
	 * @param Plugin $plugin The plugin in question.
	 * @param callable $callback The callback after the operation completes.
	 */
	public function install(Plugin $plugin, callable $callback);

	/**
	 * Updates a given Plugin. Implement this function to gain custom
	 * functionality or to work around repository quirks.
	 *
	 * @param Plugin $plugin The plugin in question.
	 * @param callable $callback The callback after the operation completes.
	 */
	public function update(Plugin $plugin, callable $callback);

}