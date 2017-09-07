<?php

/**
 * CodeMommy CachePHP
 * @author  Candison November <www.kandisheng.com>
 */

namespace CodeMommy\CachePHP;

use Redis;

/**
 * Class Cache
 * @package CodeMommy\CachePHP
 */
class Cache
{
    const TIMEOUT_ONE_SECOND  = 1; // 1
    const TIMEOUT_ONE_MINUTE  = 60; // 1 * 60
    const TIMEOUT_ONE_HOUR    = 3600; // 1 * 60 * 60
    const TIMEOUT_ONE_DAY     = 86400; // 1 * 60 * 60 * 24
    const TIMEOUT_ONE_WEEK    = 604800; // 1 * 60 * 60 * 24 * 7
    const TIMEOUT_ONE_MONTH   = 2592000; // 1 * 60 * 60 * 24 * 30
    const TIMEOUT_ONE_QUARTER = 7776000; // 1 * 60 * 60 * 24 * 90
    const TIMEOUT_ONE_YEAR    = 31536000; // 1 * 60 * 60 * 24 * 365
    const TIMEOUT_ONE_CENTURY = 3153600000; // 1 * 60 * 60 * 24 * 365 * 100
    const TIMEOUT_ONE_LIFE    = 3153600000; // 1 * 60 * 60 * 24 * 365 * 100

    const SERVER_LOCALHOST = 'localhost';

    const DRIVER_REDIS     = 'Redis';
    const DRIVER_MEMCACHED = 'Memcached';
    const DRIVER_APC       = 'APC';
    const DRIVER_XCACHE    = 'XCache';

    const PORT_REDIS     = 6379;
    const PORT_MEMCACHED = 11211;

    private static $config = null;
    private static $driver = null;
    private static $prefix = null;

    /**
     * Get Key
     *
     * @param $key
     *
     * @return string
     */
    private static function getKey($key)
    {
        return self::$prefix . $key;
    }

    /**
     * Is Driver
     *
     * @param $driver
     *
     * @return bool
     */
    private static function isDriver($driver)
    {
        if (self::$config['driver'] == $driver) {
            return true;
        }
        return false;
    }

    /**
     * Start Driver
     */
    private static function startDriver()
    {
        if (self::$driver == null) {
            if (self::isDriver(self::DRIVER_REDIS)) {
                self::$driver = new Redis();
                self::$driver->connect(self::$config['server'], self::$config['port']);
                if (isset(self::$config['password'])) {
                    self::$driver->auth(self::$config['password']);
                }
                if (isset(self::$config['database'])) {
                    self::$driver->select(self::$config['database']);
                }
            }
        }
    }

    /**
     * Set Config
     *
     * @param null $config
     */
    public static function setConfig($config = null)
    {
        self::$config = is_array($config) ? $config : array();
        self::$prefix = isset(self::$config['prefix']) ? strval(self::$config['prefix']) : '';
        self::closeDriver();
    }

    /**
     * Close Driver
     * @return null
     */
    public static function closeDriver()
    {
        if (self::$driver != null) {
            if (self::isDriver(self::DRIVER_REDIS)) {
                self::$driver->close();
            }
        }
        self::$driver = null;
        return true;
    }

    /**
     * Get Driver
     * @return null
     */
    public static function getDriver()
    {
        self::startDriver();
        return self::$driver;
    }

    /**
     * Get Data
     *
     * @param $key
     * @param $timeout
     * @param $function
     *
     * @return mixed
     */
    public static function getData($key, $timeout, $function)
    {
        $key = self::getKey($key);
        if (self::isExist($key)) {
            return unserialize(self::readValue($key));
        }
        $value = $function();
        $timeout = intval($timeout);
        self::writeValue($key, serialize($value), $timeout);
        return $value;
    }

    /**
     * Is Exist
     *
     * @param $key
     *
     * @return bool
     */
    public static function isExist($key)
    {
        $key = self::getKey($key);
        self::startDriver();
        if (self::isDriver(self::DRIVER_REDIS)) {
            return self::$driver->exists($key);
        }
        return false;
    }

    /**
     * Delete
     *
     * @param $key
     *
     * @return bool
     */
    public static function delete($key)
    {
        $key = self::getKey($key);
        self::startDriver();
        if (self::isDriver(self::DRIVER_REDIS)) {
            $result = self::$driver->delete($key);
            if ($result > 0) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * Write Value
     *
     * @param $key
     * @param $value
     * @param int $timeout
     *
     * @return bool
     */
    public static function writeValue($key, $value, $timeout = 0)
    {
        $key = self::getKey($key);
        self::startDriver();
        $timeout = intval($timeout);
        if (self::isDriver(self::DRIVER_REDIS)) {
            self::$driver->set($key, $value, $timeout);
            return true;
        }
        return false;
    }

    /**
     * Read Value
     *
     * @param $key
     * @param null $default
     *
     * @return null
     */
    public static function readValue($key, $default = null)
    {
        self::startDriver();
        if (self::isDriver(self::DRIVER_REDIS)) {
            if (!self::isExist($key)) {
                return $default;
            }
            $key = self::getKey($key);
            return self::$driver->get($key);
        }
        return $default;
    }
}