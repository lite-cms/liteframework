<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Framework interface
 *
 * @modified : 31 Aug 2022
 * @created  : 30 Aug 2022
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface FrameworkInterface
{
	/**
	 * Initialize
	 * Handles basic app.
	 *
	 * @return void
	*/
	public static function init(string $documentRoot, string $selfPath) : void;

	/**
	 * Route
	 *
	 * @return void
	*/
	public static function route(string $pattern, $callback) : void;

	/**
	 * On error
	 *
	 * @return void
	*/
	public static function onError(callable $func) : void;

	/**
	 * Run
	 *
	 * @return bool true on success/false on failure
	*/
	public static function run() : bool;
}
