<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * URL Interface
 *
 * @modified : 03 Sep 2022
 * @created  : 30 Aug 2022
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface UrlInterface
{
	/**
	 * Constructor
	 *
	 * @param string
	*/
	public function __construct(string $docRoot = '', string $selfPath = '');

	/**
	 * Set document path
	 *
	 * @param string
	 * @return self
	*/
	public function setDocumentRootPath(string $path) : UrlInterface;

	/**
	 * Set self document path (your application base directory)
	 *
	 * @param string
	 * @return self
	*/
	public function setSelfPath(string $path) : UrlInterface;

	/**
	 * Get document path
	 *
	 * @return string
	*/
	public function getDocumentRootPath() : string;

	/**
	 * Get self document path (your application base directory)
	 *
	 * @return string
	*/
	public function getSelfPath() : string;

	/**
	 * Set url
	 *
	 * @return self
	*/
	public function setUrl(string $url) : UrlInterface;

	/**
	 * Set url (alias of setUrl)
	 *
	 * @return self
	*/
	public function set(string $url) : UrlInterface;

	/**
	 * Get url path
	 *
	 * @return string
	*/
	public function getPath() : string;

	/**
	 * Get url path (alias of getPath)
	 *
	 * @return string
	*/
	public function get() : string;

	/**
	 * Get url path in array
	 *
	 * @return array
	*/
	public function getPathArray() : array;

	/**
	 * Get url path in array (alias of getPathArray)
	 *
	 * @return array
	*/
	public function getArray() : array;

	/**
	 * Get url query string
	 *
	 * @return string
	*/
	public function getQuery() : string;

	/**
	 * Get url fragment - after the hashmark #
	 *
	 * @return string
	*/
	public function getFragment() : string;

	/**
	 * Get url base path - app directory
	 *
	 * @return string
	*/
	public function getBasePath() : string;

	/**
	 * Set url base path - app directory
	 *
	 * @param string
	 * @return void
	*/
	public function setBasePath(string $path) : void;
}
