<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * LiteFramework
 *
 * @modified : 16 Aug 2022
 * @created  : 16 Aug 2022
*/

defined('LITEF_PATH') OR define('LITEF_PATH', rtrim(str_replace('\\', '/', __DIR__), '/'));

/* Set internal character encoding to UTF-8 */
if (function_exists('mb_internal_encoding') === false) {
	http_response_code(500);
	echo "'mbstring' extension is not loaded. This is required to run correctly.";
	exit(1);
}

@ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
