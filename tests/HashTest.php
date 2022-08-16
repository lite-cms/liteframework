<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Hash Tests
 *
 * ./vendor/bin/phpunit tests/HashTest.php
 *
 * @modified : 16 Aug 2022
 * @created  : 16 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Hash;

final class HashTest extends TestCase
{
	private $hash;

	protected function setUp() : void
	{
		$this->hash = new Hash();

		return;
	}

	public function testHash1() : void
	{
		$value = $this->hash->md5('hello');
		$this->assertSame($value, md5('hello'));

		$salt = 'mySalt';
		$this->hash->setSalt($salt);

		$value = $this->hash->getSalt();
		$this->assertSame($value, $salt);

		$results = [
			'md5'  => '8659b1ce366b0ebc29024400ac7dabcc',
			'sha1' => '239f3f8383e0d8f8f4bf74cec96af276791c37a5',
			'sha2' => 'ca293faccb7f03dd96ff2059374691f0dfcef2f81f83df2502de41a168c5d426',
			'sha3' => 'd04147d256558d172bc91fa85893a2784e39b73e2ab292c7f373df34805948561117547fd42f5d849cf21d0ed34bde89',
			'sha5' => '40c0d2c50a9706ae1306b53ea0778628a039b338107a8d9d37e117fa347945e2be9017f592690a3a422ee238be4638ca462ca51f63ae6e0b5871887207a02589',
		];

		$resultsLength = [
			'md5'  => 32,
			'sha1' => 40,
			'sha2' => 64,
			'sha3' => 96,
			'sha5' => 128,
		];

		$value = $this->hash->md5('hello');
		$this->assertSame($value, $results['md5']);
		$this->assertSame(strlen($value), $resultsLength['md5']);

		$value = $this->hash->sha1('hello');
		$this->assertSame($value, $results['sha1']);
		$this->assertSame(strlen($value), $resultsLength['sha1']);

		$value = $this->hash->sha2('hello');
		$this->assertSame($value, $results['sha2']);
		$this->assertSame(strlen($value), $resultsLength['sha2']);

		$value = $this->hash->sha3('hello');
		$this->assertSame($value, $results['sha3']);
		$this->assertSame(strlen($value), $resultsLength['sha3']);

		$value = $this->hash->sha5('hello');
		$this->assertSame($value, $results['sha5']);
		$this->assertSame(strlen($value), $resultsLength['sha5']);

		$value = $this->hash->sha1('hello', 'mySalt2');
		$this->assertNotSame($value, $results['sha1']);

		$value = $this->hash->sha1('message', 'mySalt2');
		$this->assertSame($value, '5d52eb0b6813febd7f7f53e7ea42ff003be6762c');

		return;
	}
}
