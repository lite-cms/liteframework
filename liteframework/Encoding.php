<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Encoding
 *
 * @modified : 16 Aug 2022
 * @created  : 20 Sep 2019
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\EncodingInterface;

class Encoding implements EncodingInterface
{
	/**
	 * To UTF8
	 *
	 * @param string
	 * @return string
	*/
	public function toUTF8(string $input) : string
	{
		return $this->toUTF8Ref($input);
	}

	/**
	 * To UTF8 by Reference
	 *
	 * @param string
	 * @return string
	*/
	public function toUTF8Ref(string& $input) : string
	{
		return \ForceUTF8\Encoding::toUTF8($input);
	}

	/**
	 * Fix to UTF8
	 *
	 * @param string
	 * @return string
	*/
	public function fixUTF8(string $input) : string
	{
		return $this->fixUTF8Ref($input);
	}

	/**
	 * Fix UTF8 by Reference
	 *
	 * @param string
	 * @return string
	*/
	public function fixUTF8Ref(string& $input) : string
	{
		return \ForceUTF8\Encoding::fixUTF8($input);
	}

	/**
	 * Is ASCII
	 *
	 * @param string
	 * @return bool true on success/bool false on failure
	*/
	public function isAscii(string $input) : bool
	{
		return $this->isAsciiRef($input);
	}

	/**
	 * Is ASCII by Reference
	 *
	 * @param string
	 * @return bool true on success/bool false on failure
	*/
	public function isAsciiRef(string& $input) : bool
	{
		return preg_match('/[^\x00-\x7F]/S', $input) !== 1 ? true : false;
	}

	/**
	 * Gets the length of a string
	 * If you need length of string in bytes you should use $encoding = '8bit'.
	 *
	 * $encoding:
	 * 	(1) UTF-8
	 *	(2) ASCII
	 *	(3) JIS
	 *	(4) CP50220
	 *	(5) 8bit
	 *	(6) 7bit
	 *	(7) ISO-8859-1
	 *	(8) UCS-2
	 *	(9) BASE64
	 *	(10) Windows-1251
	 *
	 * @param string
	 * @param string
	 * @return int
	*/
	public function strlen(string $input, string $encoding = '8bit') : int
	{
		return $this->strlenRef($input, $encoding);
	}

	/**
	 * Gets the length of a string by Reference
	 *
	 * @return int
	*/
	public function strlenRef(string& $input, string $encoding = '8bit') : int
	{
		return $this->isAsciiRef($input) === false ? mb_strlen($input, $encoding) : strlen($input);
	}
}
