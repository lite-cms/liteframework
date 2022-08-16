<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Router Interface
 *
 * @modified : 24 Feb 2020
 * @created  : 24 Feb 2020
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface RouterInterface
{
	/**
     * URL Match
	 *
	 * @param string
	 * @param bool
	 * @return bool true on success/false on failure
	*/
	public function match(string $pattern, string $url, bool $caseSensitive = false) : bool;

	/**
	 * URL Match by Reference
	 *
	 * @param string
	 * @param bool
	 * @return bool
	*/
	public function matchRef(string& $pattern, string& $url, bool& $caseSensitive = false) : bool;

	/**
	 * Get Match Params
	 *
	 * @return array
	*/
	public function getMatchParams() : array;

	/**
	 * Make
	 *
	 * @param string
	 * @param array
	 * @return string
	*/
	public function make(string $pattern, array $params = []) : string;
}
