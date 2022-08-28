<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Dispatcher Tests
 *
 * ./vendor/bin/phpunit tests/DispatcherTest.php
 *
 * @modified : 28 Aug 2022
 * @created  : 28 Aug 2022
*/

declare(strict_types=1);

defined('LITEF_PATH') OR exit('Restricted access');

use PHPUnit\Framework\TestCase;
use LiteFramework\Router;
use LiteFramework\Dispatcher;

require_once(__DIR__.'/data/ExampleClass.php');

final class DispatcherTest extends TestCase
{
	public function test1() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/');

		$check = false;

		$dispatcher->set('/', function() use(&$check) {
			$check = true;
			return 100;
		});

		$dispatcher->set('/any', function() {
			$this->assertEquals(true, false);
		});

		$this->assertEquals(true, $dispatcher->has('/'));
		$this->assertEquals(false, $dispatcher->has('/nop'));
		$this->assertEquals(true, is_callable($dispatcher->get('/')));
		$this->assertEquals(false, is_callable($dispatcher->get('/nop')));
		$this->assertEquals(null, $dispatcher->getResult());
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals(true, $check);
		$this->assertEquals(100, $dispatcher->getResult());

		return;
	}

	public function test2() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/');
		$dispatcher->set('/', 'DispatcherTestMyClass::staticIndex');
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('A', $dispatcher->getResult());
		return;
	}

	public function test3() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/');
		$dispatcher->set('/', ['DispatcherTestMyClass', 'index']);
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('B', $dispatcher->getResult());
		return;
	}

	public function test4() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/');
		$dispatcher->set('/', '\MyNameSpace\ExampleTestClass::staticIndex');
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('Test-A', $dispatcher->getResult());
		return;
	}

	public function test5() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/');
		$dispatcher->set('/', ['\MyNameSpace\ExampleTestClass', 'index']);
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('Test-B', $dispatcher->getResult());
		return;
	}

	public function test6() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/');
		$dispatcher->set('/', ['\MyNameSpace\ExampleTestClass', 'nonIndex']);
		$this->assertEquals(false, $dispatcher->run());
		$this->assertEquals(null, $dispatcher->getResult());

		return;
	}

	public function test7() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/');
		$dispatcher->set('/', '\MyNameSpace\ExampleTestClass::nonStaticIndex');
		$this->assertEquals(false, $dispatcher->run());
		$this->assertEquals(null, $dispatcher->getResult());
		return;
	}

	public function test8() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/');
		$dispatcher->set('/', 'DispatcherTestMyClass::staticMethod');
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals(null, $dispatcher->getResult());
		return;
	}

	public function testParam1() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/ali');
		$dispatcher->set('/@name', 'DispatcherTestMyClass::staticParam1');
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('ali', $dispatcher->getResult());
		return;
	}

	public function testParam2() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/ali/reza/');
		$dispatcher->set('/@name/@name2', 'DispatcherTestMyClass::staticParam2');
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('reza', $dispatcher->getResult());
		return;
	}

	public function testParam3() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/ali/reza/leila');
		$dispatcher->set('/@name/@name2/@name3/', 'DispatcherTestMyClass::staticParam3');
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('leila', $dispatcher->getResult());
		return;
	}

	public function testParam4() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/ali/reza/leila/david');
		$dispatcher->set('/@name/@name2/@name3/@name4', 'DispatcherTestMyClass::staticParam4');
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('david', $dispatcher->getResult());
		return;
	}

	public function testParam5() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/ali/reza/leila/david/deli');
		$dispatcher->set('/@name/@name2/@name3/@name4/@name5', 'DispatcherTestMyClass::staticParam5');
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('deli', $dispatcher->getResult());
		return;
	}

	public function testParam6() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/ali/reza/leila/david/deli/maryam');
		$dispatcher->set('/@name/@name2/@name3/@name4/@name5/@name6', 'DispatcherTestMyClass::staticParam6');
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('maryam', $dispatcher->getResult());
		return;
	}

	public function testParamSensitive1() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/A/ali/reza/leila');
		$dispatcher->caseSensitive = true;
		$dispatcher->set('/a/@name/@name2/@name3/', 'DispatcherTestMyClass::staticParam3');
		$this->assertEquals(false, $dispatcher->run());
		$this->assertEquals(null, $dispatcher->getResult());
		return;
	}

	public function testParamSensitive2() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/A/ali/reza/leila');
		$dispatcher->set('/a/@name/@name2/@name3/', 'DispatcherTestMyClass::staticParam3');
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('leila', $dispatcher->getResult());
		return;
	}

	public function testNParam1() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/ali');
		$dispatcher->set('/@name', ['DispatcherTestMyClass', 'NStaticParam1']);
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('ali', $dispatcher->getResult());
		return;
	}

	public function testNParam2() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/ali/reza/');
		$dispatcher->set('/@name/@name2', ['DispatcherTestMyClass', 'NStaticParam2']);
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('reza', $dispatcher->getResult());
		return;
	}

	public function testNParam3() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/ali/reza/leila');
		$dispatcher->set('/@name/@name2/@name3/', ['DispatcherTestMyClass', 'NStaticParam3']);
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('leila', $dispatcher->getResult());
		return;
	}

	public function testNParam4() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/ali/reza/leila/david');
		$dispatcher->set('/@name/@name2/@name3/@name4', ['DispatcherTestMyClass', 'NStaticParam4']);
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('david', $dispatcher->getResult());
		return;
	}

	public function testNParam5() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/ali/reza/leila/david/deli');
		$dispatcher->set('/@name/@name2/@name3/@name4/@name5', ['DispatcherTestMyClass', 'NStaticParam5']);
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('deli', $dispatcher->getResult());
		return;
	}

	public function testNParam6() : void
	{
		$router = new Router();
		$dispatcher = new Dispatcher($router, '/ali/reza/leila/david/deli/maryam');
		$dispatcher->set('/@name/@name2/@name3/@name4/@name5/@name6', ['DispatcherTestMyClass', 'NStaticParam6']);
		$this->assertEquals(true, $dispatcher->run());
		$this->assertEquals('maryam', $dispatcher->getResult());
		return;
	}
}

class DispatcherTestMyClass
{
	public static function staticIndex() : string
	{
		return 'A';
	}

	public function index() : string
	{
		return 'B';
	}

	public static function staticMethod() : void
	{
	}

	public static function staticParam1($param1) : string
	{
		return $param1;
	}

	public static function staticParam2($param1, $param2) : string
	{
		return $param2;
	}

	public static function staticParam3($param1, $param2, $param3) : string
	{
		return $param3;
	}

	public static function staticParam4($param1, $param2, $param3, $param4) : string
	{
		return $param4;
	}

	public static function staticParam5($param1, $param2, $param3, $param4, $param5) : string
	{
		return $param5;
	}

	public static function staticParam6(array $paramArray) : string
	{
		return $paramArray[5];
	}

	public function nStaticParam1($param1) : string
	{
		return $param1;
	}

	public function nStaticParam2($param1, $param2) : string
	{
		return $param2;
	}

	public function nStaticParam3($param1, $param2, $param3) : string
	{
		return $param3;
	}

	public function nStaticParam4($param1, $param2, $param3, $param4) : string
	{
		return $param4;
	}

	public function nStaticParam5($param1, $param2, $param3, $param4, $param5) : string
	{
		return $param5;
	}

	public function nStaticParam6(array $paramArray) : string
	{
		return $paramArray[5];
	}
}
