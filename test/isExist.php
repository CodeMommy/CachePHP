<?php

/**
 * @author   Candison November <www.kandisheng.com>
 */

require_once(__DIR__ . '/../source/Cache.php');

use CodeMommy\CachePHP\Cache;

$config = require_once(__DIR__ . '/config.php');
Cache::setConfig($config);
var_dump(Cache::isExist('key'));
var_dump(Cache::isExist('hello'));