<?php


namespace Sheep\Source;


use Sheep\Plugin;

interface Source {

	/**
	 * Searches for a plugin.
	 *
	 * @param string $plugin The name of the plugin.
	 * @param callable $callback The callback for receiving data.
	 * @param bool $exact Whether to return only exact matches from the repository.
	 * @return mixed
	 */
	public function search(string $plugin, callable $callback, bool $exact = false);

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