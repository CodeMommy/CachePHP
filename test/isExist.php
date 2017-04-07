<?php

/**
 * @author   Candison November <www.kandisheng.com>
 */

require_once(__DIR__ . '/../source/Cache.php');
require_once(__DIR__ . '/config.php');

use CodeMommy\CachePHP\Cache;

var_dump(Cache::isExist('key'));
var_dump(Cache::isExist('hello'));