<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Dictionary
 *
 * @modified : 02 Sep 2022
 * @created  : 01 Sep 2022
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\DictionaryInterface;

class Dictionary implements DictionaryInterface
{
	// @string - iso code
	protected $isoCode = '';

	// @array
	protected $dirs = [];

	// @array
	protected $dic = [];

	/**
	 * Constructor
	 *
	 * @param string
	 * @param string
	*/
	public function __construct(string $isoCode, string $dirPath)
	{
		$this->isoCode = $isoCode;
		$this->addDir($dirPath);
	}

	/**
	 * Add directory path
	 *
	 * @param string
	 * @return void
	*/
	public function addDir(string $dirPath) : void
	{
		$this->dirs[] = $dirPath;
		return;
	}

	/**
	 * Get
	 *
	 * @param string
	 * @param string
	 * @param array
	 * @return string
	*/
	public function get(string $filename, string $key, array $data = []) : string
	{
		if ($this->isLoaded($filename) === false) {
			$rc = $this->loadFile($filename);
			if ($rc === false)
				return '';
		}

		if (isset($this->dic[$filename][$key]) === false)
			return '';

		if (count($data) > 0)
			return vsprintf($this->dic[$filename][$key], $data);

		return $this->dic[$filename][$key];
	}

	/**
	 * Get language - iso code
	 *
	 * @return array
	*/
	public function getLanguage() : string
	{
		return $this->isoCode;
	}

	/**
	 * Get directories path
	 *
	 * @return array
	*/
	public function getDir() : array
	{
		return $this->dirs;
	}

	/**
	 * To string
	 *
	 * @return string
	*/
	public function __toString() : string
	{
		return $this->isoCode;
	}

	/**
	 * Is loaded
	 *
	 * @param bool true on success/false on failure
	*/
	protected function isLoaded(string& $filename) : bool
	{
		return isset($this->dic[$filename]) === true ? true : false;
	}

	/**
	 * Load file
	 *
	 * @param bool true on success/false on failure
	*/
	protected function loadFile(string& $filename) : bool
	{
		foreach ($this->dirs as $dir) {
			$filePath = $dir.'/'.$this->isoCode.'/'.$filename.'.php';
			if (is_file($filePath) === false)
				continue;

			if (isset($this->dic[$filename]) === false)
				$this->dic[$filename] = [];

			$items =& dictionaryLoadArrayFile_($filePath);
			if (count($items) === 0)
				continue;

			foreach ($items as $k => $v)
				$this->dic[$filename][$k] = $v;
		}

		return true;
	}
}

/**
 * Require PHP array file
 *
 * @param string
 * @return array
*/
function &dictionaryLoadArrayFile_(string& $filename) : array
{
	$data = require($filename);
	if (is_array($data) === false) {
		$arr = [];
		return $arr;
	}
	return $data;
}
