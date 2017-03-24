<?php

/**
 * @author   Candison November <www.kandisheng.com>
 */

require_once(__DIR__ . '/../source/Cache.php');

use CodeMommy\CachePHP\Cache;

$config = require_once(__DIR__ . '/config.php');
$cache = new Cache($config);
$result = $cache->getData('key', $cache::TIMEOUT_ONE_MINUTE, function () {
    var_dump('No Cache');
    return 'OK';
});
echo $result;