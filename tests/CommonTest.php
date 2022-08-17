<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Common Tests
 *
 * ./vendor/bin/phpunit tests/CommonTest.php
 *
 * @modified : 17 Aug 2022
 * @created  : 16 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;

final class CommonTest extends TestCase
{
	public function testArrayOrderBy() : void
	{
		$data = [];
		$data[] = array('vol' => 67, 'version' => 2);
		$data[] = array('vol' => 86, 'version' => 1);
		$data[] = array('vol' => 85, 'version' => 6);
		$data[] = array('vol' => 98, 'version' => 2);
		$data[] = array('vol' => 86, 'version' => 6);
		$data[] = array('vol' => 67, 'version' => 7);

		$result = LiteFramework\arrayOrderBy($data, 'vol', SORT_DESC, 'version', SORT_ASC);
		$this->assertIsArray($result);
		$this->assertSame($result[0]['vol'], 98);
		$this->assertSame($result[0]['version'], 2);
		$this->assertSame($result[5]['vol'], 67);
		$this->assertSame($result[5]['version'], 7);

		$result = LiteFramework\arrayOrderBy($data, 'vol', SORT_ASC, 'version', SORT_DESC);
		$this->assertIsArray($result);
		$this->assertSame($result[5]['vol'], 98);
		$this->assertSame($result[5]['version'], 2);
		$this->assertSame($result[0]['vol'], 67);
		$this->assertSame($result[0]['version'], 7);

		$data = [];
		$data[] = array('vol' => 10, 'version' => 2);
		$data[] = array('vol' => 10, 'version' => 3);
		$data[] = array('vol' => 10, 'version' => 1);
		$data[] = array('vol' => 10, 'version' => 4);

		$result = LiteFramework\arrayOrderBy($data, 'version', SORT_DESC);
		$this->assertIsArray($result);
		$this->assertSame($result[0]['version'], 4);
		$this->assertSame($result[1]['version'], 3);
		$this->assertSame($result[2]['version'], 2);
		$this->assertSame($result[3]['version'], 1);

		$result = LiteFramework\arrayOrderBy($data, 'version', SORT_ASC);
		$this->assertIsArray($result);
		$this->assertSame($result[0]['version'], 1);
		$this->assertSame($result[1]['version'], 2);
		$this->assertSame($result[2]['version'], 3);
		$this->assertSame($result[3]['version'], 4);

		$data = [];
		$data[] = array('id' => 22);
		$data[] = array('id' => 23);
		$data[] = array('id' => 20);
		$data[] = array('id' => 21);

		$result = LiteFramework\arrayOrderBy($data, 'id', SORT_ASC);
		$this->assertIsArray($result);
		$this->assertSame($result[0]['id'], 20);
		$this->assertSame($result[1]['id'], 21);
		$this->assertSame($result[2]['id'], 22);
		$this->assertSame($result[3]['id'], 23);

		return;
	}

	public function testUploadSize() : void
	{
		$result = LiteFramework\getMaxFileUploadSize();
		$this->assertIsInt($result);

		$result = LiteFramework\convertPhpSizeToBytes('5M');
		$this->assertIsInt($result);
		$this->assertGreaterThan(4999999, $result);

		$result = LiteFramework\convertPhpSizeToBytes('999M');
		$this->assertIsInt($result);
		$this->assertGreaterThan(999999998, $result);

		return;
	}

	public function testGetValue() : void
	{
		$data = [
			'key1' => 'value',
			'key2' => 2,
			'key3' => [],
		];

		$result = LiteFramework\getValue($data, 'key1');
		$this->assertSame($result, 'value');

		$result = LiteFramework\getValue($data, 'key10');
		$this->assertSame($result, false);

		$result = LiteFramework\getValue($data, 'key10', 100);
		$this->assertSame($result, 100);

		$result = LiteFramework\getValue($data, 'key2');
		$this->assertSame($result, 2);

		$result = LiteFramework\getValue($data, 'key3');
		$this->assertIsArray($result);

		$result = LiteFramework\getValue($data, 'key4', []);
		$this->assertIsArray($result);

		return;
	}
}
