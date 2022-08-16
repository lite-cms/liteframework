<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Random Interface
 *
 * @modified : 14 Dec 2020
 * @created  : 17 Oct 2019
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface RandomInterface
{
	/**
	 * Random Bytes Generator
	 *
	 * @param int $length
	 * @param bool $toHex
	 * return string
	*/
	public function bytes(int $length = 32, bool $toHex = true) : string;

	/**
	 * Random Number Generator
	 *
	 * @param int
	 * @param int
	 * return int
	*/
	public function int(int $min, int $max) : int;
}
