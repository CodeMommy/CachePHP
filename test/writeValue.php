<?php

/**
 * @author   Candison November <www.kandisheng.com>
 */

require_once(__DIR__ . '/../source/Cache.php');
require_once(__DIR__ . '/config.php');

use CodeMommy\CachePHP\Cache;

Cache::writeValue('key', 'value', Cache::TIMEOUT_ONE_MINUTE);