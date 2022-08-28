<?php

namespace MyNameSpace;

defined('LITEF_PATH') OR exit('Restricted access');

class ExampleTestClass
{
	public static function staticIndex() : string
	{
		return 'Test-A';
	}

	public function index() : string
	{
		return 'Test-B';
	}
}
