<?php

/**
 * @author   Candison November <www.kandisheng.com>
 */

require_once(__DIR__ . '/../source/Cache.php');

use CodeMommy\CachePHP\Cache;

// Redis
$config = array(
    'driver'   => Cache::DRIVER_REDIS,
    'server'   => Cache::SERVER_LOCALHOST,
    'port'     => Cache::PORT_REDIS,
    'password' => '',
    'database' => 0,
    'prefix'   => 'CachePHP'
);

Cache::setConfig($config);