<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Dictionary Interface
 *
 * @modified : 02 Sep 2022
 * @created  : 01 Sep 2022
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface DictionaryInterface
{
	/**
	 * Constructor
	 *
	 * @param string
	 * @param string
	*/
	public function __construct(string $isoCode, string $dirPath);

	/**
	 * Add directory path
	 *
	 * @param string
	 * @return void
	*/
	public function addDir(string $dirPath) : void;

	/**
	 * Get
	 *
	 * @param string
	 * @param string
	 * @param array
	 * @return string
	*/
	public function get(string $filename, string $key, array $data = []) : string;

	/**
	 * Get language - iso code
	 *
	 * @return array
	*/
	public function getLanguage() : string;

	/**
	 * Get directories path
	 *
	 * @return array
	*/
	public function getDir() : array;
}
