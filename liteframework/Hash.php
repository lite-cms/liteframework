<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Hash
 *
 * @modified : 01 Feb 2022
 * @created  : 16 Oct 2019
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\HashInterface;

class Hash implements HashInterface
{
	// @string
	protected $salt = '';

	/**
	 * Set salt
	 *
	 * @param string
	 * @return void
	*/
	public function setSalt(string $salt) {
		$this->salt = $salt;
		return;
	}

	/**
	 * Get salt
	 *
	 * @return string
	*/
	public function getSalt() : string {
		return $this->salt;
	}

	/**
	 * MD5
	 *
	 * @param string
	 * @return string | length 32
	*/
	public function md5(string $input, string $salt = '') : string {
		$s = $salt !== '' ? $salt : $this->salt;
		return md5($input.$s);
	}

	/**
	 * SHA1
	 *
	 * @param string
	 * @return string | length 40
	*/
	public function sha1(string $input, string $salt = '') : string {
		$s = $salt !== '' ? $salt : $this->salt;
		return hash('sha1', $input.$s);
	}

	/**
	 * SHA2
	 *
	 * @param string
	 * @return string | length 64
	*/
	public function sha2(string $input, string $salt = '') : string {
		$s = $salt !== '' ? $salt : $this->salt;
		return hash('sha256', $input.$s);
	}

	/**
	 * SHA3 - 384
	 *
	 * @param string
	 * @return string | length 96
	*/
	public function sha3(string $input, string $salt = '') : string {
		$s = $salt !== '' ? $salt : $this->salt;
		return hash('sha384', $input.$s);
	}

	/**
	 * SHA5 - 512
	 *
	 * @param string
	 * @return string | length 128
	*/
	public function sha5(string $input, string $salt = '') : string {
		$s = $salt !== '' ? $salt : $this->salt;
		return hash('sha512', $input.$s);
	}
}
