<?php


namespace Sheep\Source;


use Sheep\Plugin;

interface Source {

	/**
	 * Searches for a plugin.
	 *
	 * The signature of the callback:
	 * function(Sheep\Plugin[])
	 *
	 * @param string $plugin The name of the plugin.
	 * @param callable $callback The callback for receiving data.
	 * @return mixed
	 */
	public function search(string $plugin, callable $callback);

	/**
	 * Resolves a plugin from identifying data of the source's choosing.
	 * Data from the regular expression returned by regex() is fed to here,
	 * so regex wisely.
	 *
	 * The signature of the callback:
	 * function(Sheep\Plugin[])
	 *
	 * @param array $data
	 * @param callable $callback
	 * @return mixed
	 */
	public function resolve(array $data, callable $callback);

	/**
	 * Installs a given Plugin. Implement this function to gain custom
	 * functionality or to work around repository quirks, e.g. requiring
	 * a cookie to download a plugin.
	 *
	 * @param Plugin $plugin The plugin in question.
	 * @param callable $callback The callback after the operation completes.
	 * @return mixed
	 */
	public function install(Plugin $plugin, callable $callback);

	/**
	 * Updates a given Plugin. Implement this function to gain custom
	 * functionality or to work around repository quirks.
	 *
	 * @param Plugin $plugin The plugin in question.
	 * @param callable $callback The callback after the operation completes.
	 * @return mixed
	 */
	public function update(Plugin $plugin, callable $callback);

	/**
	 * Returns a RegEx for matching search / install queries to repositories.
	 *
	 * @return string
	 */
	public function regex() : string;

}