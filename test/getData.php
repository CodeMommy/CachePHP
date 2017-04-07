<?php

/**
 * @author   Candison November <www.kandisheng.com>
 */

require_once(__DIR__ . '/../source/Cache.php');
require_once(__DIR__ . '/config.php');

use CodeMommy\CachePHP\Cache;

$result = Cache::getData('key', Cache::TIMEOUT_ONE_MINUTE, function () {
    var_dump('No Cache');
    return 'OK';
});
echo $result;