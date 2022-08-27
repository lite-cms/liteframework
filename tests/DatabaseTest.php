<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Database Tests
 *
 * ./vendor/bin/phpunit tests/DatabaseTest.php
 *
 * @modified : 27 Aug 2022
 * @created  : 26 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Database;
use LiteFramework\SqlTableBuilder;

final class DatabaseTest extends TestCase
{
	public function testDb1() : void
	{
		$config = [
			'drive' => 'sqlite',
			'database' => __DIR__.'/test-db.db',
		];

		$table = [
			'test' => [
				'id' => ['type' => 'INT(10)', 'unsigned' => true, 
						 'autoincrement' => true, 'primary_key' => true],
				'char'    => ['type' => 'CHAR(100)', 'null' => true],
				'varchar' => ['type' => 'VARCHAR(100)', 'null' => true],
				'int'     => ['type' => 'int(10)', 'default' => 0],
				'float'   => ['type' => 'float(2,2)', 'default' => '00.00'],
				'_options' => ['engine' => 'InnoDB'],
				'_index' => [
					'index_int' => ['int' => 'asc'],
				],
			],
		];

		// Remove
		if (is_file($config['database']) === true)
			unlink($config['database']);

		// New
		$db = new Database();
		$builder = new SqlTableBuilder();

		$this->assertEquals(
			true,
			$db->setConfig($config)
		);

		$this->assertEquals(
			true,
			$db->connect()
		);

		$this->assertEquals(
			true,
			$builder->setSchema($table)
		);

		$sqlCodes = $builder->build('sqlite');
		$this->assertIsArray($sqlCodes);

		foreach ($sqlCodes as $code) {
			$this->assertNotEquals(
				false,
				$db->query($code)
			);
		}

		$this->assertEquals(0, $db->id());

		$this->assertNotEquals(
			false,
			$db->query("INSERT INTO `test` (`char`, `varchar`, `int`, `float`) VALUES 
			('hello', 'world', '1010', '12.09')")
		);

		$this->assertEquals(1, $db->id());

		$result = $db->select('test', '*', ['id' => 1]);
		$this->assertIsArray($result);
		$this->assertEquals(1, count($result));
		$this->assertEquals('hello', $result[0]['char']);
		$this->assertEquals(12.09, $result[0]['float']);

		$data = [
			'char' => 'Persianicon',
			'varchar' => 'PHP',
			'int'   => 11,
			'float' => 2.1,
		];

		$result = $db->insert('test', $data);
		$this->assertIsObject($result);
		$this->assertEquals(2, $db->id());

		$result = $db->select('test2', '*', ['id' => 1]);
		$this->assertIsArray($result);
		$this->assertEquals(0, count($result));

		$result = $db->select('test', ['id', 'char', 'int'], ['id[>]' => 1]);
		$this->assertIsArray($result);
		$this->assertEquals(1, count($result));
		$this->assertEquals(2, $result[0]['id']);
		$this->assertEquals('Persianicon', $result[0]['char']);
		$this->assertEquals(11, $result[0]['int']);

		$data = [
			'int'   => 22,
			'float' => 1,
		];

		$result = $db->update('test', $data, ['id' => 2]);
		$this->assertIsObject($result);
		$this->assertEquals(1, count((array)$result));

		$result = $db->select('test', ['id', 'int'], ['id' => 2]);
		$this->assertIsArray($result);
		$this->assertEquals(1, count($result));
		$this->assertEquals(22, $result[0]['int']);

		return;
	}
}
