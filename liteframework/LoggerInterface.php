<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Logger interface
 *
 * @modified : 16 Aug 2022
 * @created  : 01 Feb 2022
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface LoggerInterface extends \Psr\Log\LoggerInterface
{
	/**
	 * Based on Psr\Log\LoggerInterface
	 * @https://www.php-fig.org/psr/psr-3/
	*/

	/**
	 * Get logs filename
	 *
	 * @return string
	*/
	public function getLogPath() : string;
}
