<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Dispatcher interface
 *
 * @modified : 28 Aug 2022
 * @created  : 28 Aug 2022
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\RouterInterface;

interface DispatcherInterface
{
	/**
	 * Constructor
	 *
	 * @param object RouterInterface
	*/
	public function __construct(RouterInterface& $router, string $urlPath = '');

	/**
	 * Has event
	 *
	 * @param string
	 * @return bool true on success/false on failure
	*/
	public function has(string $eventsName) : bool;

	/**
	 * Set event
	 *
	 * @param string
	 * @param mixed string class name|callback function
	 * @return void
	*/
	public function set(string $eventsName, $callback) : void;

	/**
	 * Get event
	 *
	 * @param string
	 * @return mixed bool false|string class name|callback function
	*/
	public function get(string $eventsName);

	/**
	 * Remove event
	 *
	 * @param string
	 * @return bool true on success/false on failure
	*/
	public function remove(string $eventsName) : bool;

	/**
	 * Run
	 *
	 * @return bool true on success/false on failure
	*/
	public function run() : bool;

	/**
	 * Get callback execute result
	 *
	 * @return mixed (default is null)
	*/
	public function getResult();
}
