<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Config Tests
 *
 * ./vendor/bin/phpunit tests/ConfigTest.php
 *
 * @modified : 17 Aug 2022
 * @created  : 16 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Config;

final class ConfigTest extends TestCase
{
	private $config;

	private $exampleConfigFile = __DIR__.'/data/confing-test.php';

	protected function setUp() : void
	{
		$this->config = new Config();

		return;
	}

	public function testConfig1() : void
	{
		$this->assertEquals(
			true,
			$this->config->loadFromFile($this->exampleConfigFile)
		);

		$this->assertEquals(
			false,
			$this->config->loadFromFile('conf')
		);

		$result = $this->config->get('char');
		$this->assertSame($result, 'Hello');

		$result = $this->config->get('bool');
		$this->assertSame($result, true);

		$result = $this->config->get('null');
		$this->assertNull($result);

		$result = $this->config->get('int');
		$this->assertIsInt($result);
		$this->assertSame($result, 123456789);

		$result = $this->config->get('float');
		$this->assertSame($result, 12.34);

		$result = $this->config->get('Array');
		$this->assertIsArray($result);
		$this->assertArrayHasKey('hello', $result);
		$this->assertSame($result['hello'], 'world');

		$result = $this->config->get('none');
		$this->assertSame($result, false);

		$result = $this->config->set('myKey', 1010);
		$this->assertSame($result, true);

		$result = $this->config->get('mykey');
		$this->assertSame($result, 1010);

		$result = $this->config->get('myKEy');
		$this->assertSame($result, 1010);

		$result = $this->config->has('myKEy');
		$this->assertSame($result, true);

		$result = $this->config->has('myKEy2');
		$this->assertSame($result, false);

		$keyValue = [
			'a1' => 'b1',
			'A2' => 'b2',
			'a3' => 'b3',
			'A4' => 'b4',
		];

		$result = $this->config->loadFromArray($keyValue);
		$this->assertSame($result, true);

		$result = $this->config->get('A1');
		$this->assertSame($result, 'b1');

		$result = $this->config->setMultiple(['A1' => 'c1']);
		$this->assertSame($result, true);

		$result = $this->config->get('a1');
		$this->assertSame($result, 'c1');

		$result = $this->config->getMultiple(['A1', 'c1']);
		$this->assertSame(count($result), 0);

		$result = $this->config->getMultiple(['A1', 'a2', 'A3', 'a4']);
		$this->assertSame(count($result), 4);

		$result = $this->config->getMultiple(['A1', 'a2', 'A3', 'a4', 'A4']);
		$this->assertSame(count($result), 4);

		$result = $this->config->getMultiple(['A1', 'a2', 'A3', 'a4', ' ']);
		$this->assertSame(count($result), 0);

		$result = $this->config->get('A3');
		$this->assertSame($result, 'b3');

		$result = $this->config->set('', 1);
		$this->assertSame($result, false);

		$result = $this->config->set(' ', 1);
		$this->assertSame($result, false);

		$result = $this->config->setMultiple([[], '1']);
		$this->assertSame($result, false);

		$result = $this->config->setMultiple([1, '1']);
		$this->assertSame($result, false);

		$result = $this->config->setMultiple(['k1' => 'v1', 'k2']);
		$this->assertSame($result, false);

		$result = $this->config->has('k1');
		$this->assertSame($result, true);

		$result = $this->config->has('k2');
		$this->assertSame($result, false);

		$result = $this->config->set('the_key', ['value']);
		$this->assertSame($result, true);

		$result = $this->config->remove('A1');
		$this->assertSame($result, true);

		$result = $this->config->remove('A1');
		$this->assertSame($result, false);

		$result = $this->config->has('al');
		$this->assertSame($result, false);

		$result = $this->config->has('bool');
		$this->assertSame($result, true);

		$result = $this->config->getAll();
		$this->assertIsArray($result);
		$this->assertArrayHasKey('k1', $result);
		$this->assertArrayHasKey('bool', $result);

		$result =& $this->config->getAllRef();
		$this->assertIsArray($result);
		$this->assertArrayHasKey('a2', $result);
		$this->assertArrayHasKey('a4', $result);

		return;
	}
}
