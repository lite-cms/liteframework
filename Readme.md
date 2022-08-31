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

Framework::init($_SERVER['DOCUMENT_ROOT'], __DIR__);

Framework::route('/', function() {
	Framework::$response->write('Hello, World!');
	Framework::$response->send();
});

Framework::run();
```

#### Custom error page

```
Framework::onError(function(int $statusCode, string $statusMessage) {
	Framework::$response->status($statusCode);
	Framework::$response->write('<h1>Custom Error '.$statusCode.'</h1>');
	Framework::$response->write('<p>'.$statusMessage.'</p>');
	Framework::$response->send();
});
```

