# Lite Framework

## Installation

```
composer require lite-cms/liteframework
```

For Apache .htaccess:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

## Examples

### Basic

```php
require('vendor/autoload.php');

use LiteFramework\Framework;

Framework::init($_SERVER['DOCUMENT_ROOT'], dirname($_SERVER['SCRIPT_FILENAME']));

Framework::route('/', function() {
	Framework::$response->write('Hello, World!');
	Framework::$response->send();
});

Framework::run();
```
#### Custom error page

```php
Framework::onError(function(int $statusCode, string $statusMessage) {
	Framework::$response->status($statusCode);
	Framework::$response->write('<h1>Custom Error '.$statusCode.'</h1>');
	Framework::$response->write('<p>'.$statusMessage.'</p>');
	Framework::$response->send();
});
```

### Request

```php
use LiteFramework\Request;

$request = new Request();
$request->init();

if ($request->hasError() === true) {
	// error handling
}

echo $request->ip();
echo $request->url();
echo $request->ajax();
// more ...
```

### Response

```php
use LiteFramework\Response;

$response = new Response();
$response->init();

$response->status(200);
$response->write('Hello, World!');
$response->send();
// more ...
```

### Router

```php
use LiteFramework\Router;

$router = new Router();

$rc = $router->match('/hello', '/hello');
var_dump($rc); // true

$rc = $router->match('/users/@name', '/users/ali');
var_dump($rc); // true

$rc = $router->match('/news/[0-9]+', '/news/1234');
var_dump($rc); // true

$rc = $router->match('/news/[0-9]+', '/news/12a4');
var_dump($rc); // false

$rc = $router->match('/blog(/@year(/@month(/@day)))', '/blog/2020/11');
var_dump($rc); // true

$url = $router->make('/route/@id/user/@name', [1000, 'ali']);
echo $url; // /route/1000/user/ali

$url = $router->make('/path/*', [10, 20, 30]);
echo $url; // /path/10/20/30
```

### Dispatcher

```php
use LiteFramework\Dispatcher;
use LiteFramework\Router;
use LiteFramework\Url;

$url = new Url($_SERVER['DOCUMENT_ROOT'], dirname($_SERVER['SCRIPT_FILENAME']));
$url->setUrl('/1');

$router = new Router();
$dispatcher = new Dispatcher($router, $url->getPath());

$dispatcher->set('/1', 'MyClass::staticMethod');
$dispatcher->set('/2', ['MyClass', 'method']);
$dispatcher->set('/3', function() {
	echo 'callback';
});

$dispatcher->run();
```

```php
class MyClass
{
	public static function staticMethod() {
		echo 'static';
	}

	public function method() {
		echo 'non-static';
	}
}
```

### Database

#### SQL table builder

```php
use LiteFramework\SqlTableBuilder;
use LiteFramework\Database;

$table = [
	'test_table' => [
		'id' => [
			'type' => 'INT(10)',
			'unsigned' => true,
			'autoincrement' => true,
			'primary_key' => true
		],
		'key'    => ['type' => 'CHAR(100)', 'null' => true],
		'value'  => ['type' => 'VARCHAR(100)', 'null' => true],
		'_index' => [
			'index_name' => ['key' => 'asc'],
		],
	],
];

$builder = new SqlTableBuilder();
$builder->setSchema($table);
$sqlCodes = $builder->build('sqlite'); // sqlite|mysql|mariadb

$db = new Database();
$db->setConfig([
	'drive' => 'sqlite',
	'database' =>'mydb.db',
]);

foreach ($sqlCodes as $code) {
	$db->query($code);
	echo "$code\n";
}
```

Output (SQLite):

```sqlite
CREATE TABLE IF NOT EXISTS `test_table` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `key` TEXT,
  `value` TEXT);

CREATE  INDEX IF NOT EXISTS `test_table_index_name` ON `test_table` (`key` ASC);
```

Output (MySQL, MariaDB):

```mysql
CREATE TABLE IF NOT EXISTS `test_table` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` CHAR(100),
  `value` VARCHAR(100),
  PRIMARY KEY (`id`),
 INDEX `test_table_index_name` (`key` ASC))
 ENGINE = InnoDB;
```

#### Insert

```php
$db->insert('test_table', [
	'key' => 'Hello',
	'value' => 'World',
]);

if ($db->error() !== null) {
	// error handling
}
```

#### Select

```php
$result = $db->select('test_table', '*', ['key' => 'Hello']);

if ($db->error() !== null) {
    // error handling
}

print_r($result);
// more ...
```

Output:

```php
Array
(
    [0] => Array
        (
            [id] => 1
            [key] => Hello
            [value] => World
        )

)
```

### Logger

```php
use LiteFramework\Logger;

$logger = new Logger('logs.log');
$logger->error('message', ['file' => __FILE__]);
// more ...
```

### Filesystem

```php
use LiteFramework\Filesystem;

$fs = new Filesystem();

if ($fs->isFile('test.txt') === false) {
	$contents = 'Hello';
	$fs->write('test.txt', $contents, 0644);
}

if ($fs->isReadable('test.txt') === false) {
	$fs->chmod(0644);
}

$contents = null;
$fs->read('test.txt', $contents);

echo $contents;
// more ...
```

Output:

```
Hello
```

### Hash

```php
use LiteFramework\Hash;

$hash = new Hash();
$hash->setSalt('my-salt');

echo $hash->md5('input'); // c1957df5e4ec324fd3e0f44b0baaa430
echo $hash->sha2('input'); // 606ed6b1d650ad4ed4933f4ba9010eb384e1fe677480b20a3d428cee3c46422b
// more ...
```

### Encoding

```php
use LiteFramework\Encoding;

$encoding = new Encoding();

$rc = $encoding->isAscii('سلام دنیا');
var_dump($rc); // false

$rc = $encoding->isAscii('Hello, World!');
var_dump($rc); // true

$length = $encoding->strlen('سلام دنیا');
echo $length; // 17
// more ...
```

### Random

```php
use LiteFramework\Random;

$random = new Random();

$bytes = $random->bytes(32, $toHex = true);
echo $bytes; // 8aed6defbbb7349e4e074e68117fb0a614fe169897d13bcad57a8b11a8b3716e
// more ...
```

### Loader

```php
use LiteFramework\Loader;

$myVar = 'hello';
Loader::set('myVar', $myVar);

$val = Loader::get('myVar');
echo $val; // hello

$language = 'PHP';
Loader::setRef('language', $language); // Passing by Reference

$language = 'C++';
$val =& Loader::getRef('language'); // Get Reference
echo $val; // C++
// more ...
```

## Special thanks

* [Flight](https://github.com/mikecao/flight)
* [Medoo](https://github.com/catfan/Medoo)
* [Monolog](https://github.com/Seldaek/monolog)
