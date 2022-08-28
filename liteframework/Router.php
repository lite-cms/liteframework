<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Flight
 * MIT License
 * Copyright (c) 2011 Mike Cao <mike@mikecao.com>
 * http://flightphp.com/
*/

/**
 * Router
 *
 * Based on Flight Framework Router
 *
 * @modified : 28 Aug 2022
 * @created  : 24 Feb 2020
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\RouterInterface;

class Router implements RouterInterface
{
	protected $params = [];
	protected $splat = '';

	/**
     * URL Match
	 *
	 * @param string
	 * @param bool
	 * @return bool true on success/false on failure
	*/
	public function match(string $pattern, string $url, bool $caseSensitive = false) : bool
	{
		return $this->matchRef($pattern, $url, $caseSensitive);
	}

	/**
	 * URL Match by Reference
	 *
	 * @param string
	 * @param bool
	 * @return bool
	*/
	public function matchRef(string& $pattern, string& $url, bool& $caseSensitive = false) : bool
	{
		$this->params = [];
		$this->splat = '';

		// Wildcard or exact match
		if ($pattern === '*' || $pattern === $url)
			return true;

		$ids = [];
		$lastChar = substr($pattern, -1);

		// Get splat
		if ($lastChar === '*') {
			$n = 0;
			$len = strlen($url);
			$count = substr_count($pattern, '/');

			for ($i = 0; $i < $len; $i++) {
				if ($url[$i] === '/')
					$n++;
				if ($n == $count)
					break;
			}

			$this->splat = (string)substr($url, $i+1);
		}

		// Build the regex for matching
		$regex = str_replace([')', '/*'], [')?', '(/?|/.*?)'], $pattern);

		$regex = preg_replace_callback(
			'#@([\w]+)(:([^/\(\)]*))?#',
			function($matches) use (&$ids) {
				$ids[$matches[1]] = null;
				if (isset($matches[3]))
					return '(?P<'.$matches[1].'>'.$matches[3].')';
				return '(?P<'.$matches[1].'>[^/\?]+)';
			},
			$regex
		);

		if ($lastChar == '/')
			$regex .= '?'; // Fix trailing slash
		else
			$regex .= '/?'; // Allow trailing slash

		// Attempt to match route and named parameters
		if (preg_match('#^'.$regex.'(?:\?.*)?$#'.(($caseSensitive) ? '' : 'i'), $url, $matches)) {
			foreach ($ids as $k => $v)
				$this->params[$k] = (array_key_exists($k, $matches)) ? urldecode($matches[$k]) : null;
			return true;
		}

		return false;
	}

	/**
	 * Get Match Params
	 *
	 * @return array
	*/
	public function getMatchParams() : array
	{
		if ($this->splat !== '')
			$this->params[] = $this->splat;
		return $this->params;
	}

	/**
	 * Make
	 *
	 * @param string
	 * @param array
	 * @return string
	*/
	public function make(string $pattern, array $params = []) : string
	{
		$hasStar = strpos($pattern, '*') === false ? false : true;
		if ($hasStar === false && strpos($pattern, '@') === false)
			return $pattern;

		$url = $pattern;
		$err = false;
		$i = 0;
		$params = array_merge($params);
		$regex = str_replace([')', '/*'], [')?', '(/?|/.*?)'], $pattern);
		preg_replace_callback(
			'#@([\w]+)(:([^/\(\)]*))?#',
			function($matches) use (&$url, &$params, &$i, &$err, &$hasStar) {
				if ($err === true)
					return false;

				if (array_key_exists($i, $params) === false) {
					$err = true;
					return false;
				}

				$search = $matches[0];
				if (is_array($search) === false && is_string($search) === false)
					$search = strval($search);

				$replace = $params[$i];
				if (is_array($replace) === false && is_string($replace) === false)
					$replace = strval($replace);

				$url = str_replace($search, $replace, $url);
				if ($hasStar === true)
					unset($params[$i]);
				$i += 1;
				return true;
			},
			$regex
		);

		if ($err === true)
			return '';

		if ($hasStar === true) {
			$s = implode('/', $params);
			$url = str_replace('*', $s, $url);
		}

		return $url;
	}
}
