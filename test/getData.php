<?php

/**
 * @author   Candison November <www.kandisheng.com>
 */

require_once(__DIR__ . '/../source/Cache.php');

use CodeMommy\CachePHP\Cache;

$config = require_once(__DIR__ . '/config.php');
Cache::setConfig($config);
$result = Cache::getData('key', Cache::TIMEOUT_ONE_MINUTE, function () {
    var_dump('No Cache');
    return 'OK';
});
echo $result;