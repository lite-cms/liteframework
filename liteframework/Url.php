<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * URL
 *
 * @modified : 30 Aug 2022
 * @created  : 30 Aug 2022
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\UrlInterface;

class Url implements UrlInterface
{
	// @string
	public $mbEncoding = '8bit';

	// @array
	public $filterChars = [
		'\\',
		"'",
		'"',
	];

	// @string - php document root
	protected $docRootPath = '';

	// @string - self document root (app directory)
	protected $selfPath = '';

	// @string - url path
	protected $urlPath = '';

	// @string - url base path (app directory path)
	protected $baseUrlPath = null;

	// @string - url query string
	protected $query = '';

	// @string - url fragment
	protected $fragment = '';

	// @array - url path in array
	protected $urlPathArray = null;

	/**
	 * Constructor
	 *
	 * @param string
	*/
	public function __construct(string $docRoot = '', string $selfPath = '')
	{
		if ($docRoot !== '')
			$this->setDocumentRootPath($docRoot);

		if ($selfPath !== '')
			$this->setSelfPath($selfPath);

		return;
	}

	/**
	 * Set document path
	 *
	 * @param string
	 * @return self
	*/
	public function setDocumentRootPath(string $path) : UrlInterface
	{
		$path = str_replace('\\', '/', $path);
		$this->docRootPath = rtrim($path, '/');
		$this->baseUrlPath = null; // reset
		return $this;
	}

	/**
	 * Set self document path (your application base directory)
	 *
	 * @param string
	 * @return self
	*/
	public function setSelfPath(string $path) : UrlInterface
	{
		$path = str_replace('\\', '/', $path);
		$this->selfPath = rtrim($path, '/');
		$this->baseUrlPath = null; // reset
		return $this;
	}

	/**
	 * Get document path
	 *
	 * @return string
	*/
	public function getDocumentRootPath() : string
	{
		return $this->docRootPath;
	}

	/**
	 * Get self document path (your application base directory)
	 *
	 * @return string
	*/
	public function getSelfPath() : string
	{
		return $this->selfPath;
	}

	/**
	 * Get url path
	 *
	 * @return string
	*/
	public function getPath() : string
	{
		return $this->urlPath;
	}

	/**
	 * Get url path (alias of getPath)
	 *
	 * @return string
	*/
	public function get() : string
	{
		return $this->urlPath;
	}

	/**
	 * Get url path in array
	 *
	 * @return array
	*/
	public function getPathArray() : array
	{
		if ($this->urlPathArray === null)
			$this->urlPathToArray();
		return $this->urlPathArray;
	}

	/**
	 * Get url path in array (alias of getPathArray)
	 *
	 * @return array
	*/
	public function getArray() : array
	{
		return $this->getPathArray();
	}

	/**
	 * Get url query string
	 *
	 * @return string
	*/
	public function getQuery() : string
	{
		return $this->query;
	}

	/**
	 * Get url fragment - after the hashmark #
	 *
	 * @return string
	*/
	public function getFragment() : string
	{
		return $this->fragment;
	}

	/**
	 * Get url base path
	 *
	 * @return string
	*/
	public function getBasePath() : string
	{
		if ($this->baseUrlPath === null)
			$this->findBaseUrlPath();
		return $this->baseUrlPath;
	}

	/**
	 * To string
	 *
	 * @return string
	*/
	public function __toString() : string
	{
		return $this->urlPath;
	}

	/**
	 * Set url
	 *
	 * @return self
	*/
	public function setUrl(string $url) : UrlInterface
	{
		$this->urlPath  = '';
		$this->query    = '';
		$this->fragment = '';
		$this->urlPathArray = null; // reset
		$lastCharIsSlash = false;
		$isQuery = false;
		$isFragment = false;
		$urlLen = mb_strlen($url, $this->mbEncoding);

		for ($i=0; $i<$urlLen; ++$i) {
			if (in_array($url[$i], $this->filterChars) === true)
				continue;

			if ($url[$i] === '/') {
				if ($lastCharIsSlash === true)
					continue;
				$lastCharIsSlash = true;
			}
			else if ($lastCharIsSlash === true) {
				$lastCharIsSlash = false;
			}

			if ($url[$i] === '?') {
				$isQuery = true;
				continue;
			}

			if ($url[$i] === '#') {
				$isQuery = false;
				$isFragment = true;
				continue;
			}

			if ($isQuery === true)
				$this->query .= $url[$i];
			else if ($isFragment === true)
				$this->fragment .= $url[$i];
			else
				$this->urlPath .= $url[$i];
		}

		$this->removeBasePath();

		return $this;
	}

	/**
	 * Set url
	 *
	 * @return self
	*/
	public function set(string $url) : UrlInterface
	{
		return $this->setUrl($url);
	}

	/**
	 * Remove base url path
	 *
	 * @return void
	*/
	protected function removeBasePath() : void
	{
		if ($this->baseUrlPath === null)
			$this->findBaseUrlPath();

		if ($this->baseUrlPath === '')
			return;

		$this->urlPath = str_replace($this->baseUrlPath, '', $this->urlPath);
		return;
	}

	/**
	 * Find the base url path
	 *
	 * @return void
	*/
	protected function findBaseUrlPath() : void
	{
		$this->baseUrlPath = str_replace($this->docRootPath, '', $this->selfPath);
		if ($this->baseUrlPath === '/')
			$this->baseUrlPath = '';
		return;
	}

	/**
	 * Url path to array
	 *
	 * @return void
	*/
	protected function urlPathToArray() : void
	{
		if ($this->urlPath === '' || $this->urlPath === '/') {
			$this->urlPathArray = [''];
			return;
		}

		$urlArray = explode('/', $this->urlPath);
		if (count($urlArray) === 0) {
			$this->urlPathArray = [''];
			return;
		}

		$this->urlPathArray = [];
		foreach ($urlArray as $item) {
			if ($item === '')
				continue;
			$this->urlPathArray[] = $item;
		}

		return;
	}
}
