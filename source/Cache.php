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
    const TIMEOUT_ONE_SECOND  = 1;
    const TIMEOUT_ONE_MINUTE  = 1 * 60;
    const TIMEOUT_ONE_HOUR    = 1 * 60 * 60;
    const TIMEOUT_ONE_DAY     = 1 * 60 * 60 * 24;
    const TIMEOUT_ONE_MONTH   = 1 * 60 * 60 * 24 * 30;
    const TIMEOUT_ONE_QUARTER = 1 * 60 * 60 * 24 * 90;
    const TIMEOUT_ONE_YEAR    = 1 * 60 * 60 * 24 * 365;
    const TIMEOUT_ONE_CENTURY = 1 * 60 * 60 * 24 * 365 * 100;
    const TIMEOUT_ONE_LIFE    = 1 * 60 * 60 * 24 * 365 * 100;

    const SERVER_LOCALHOST = 'localhost';

    const DRIVER_REDIS     = 'Redis';
    const DRIVER_MEMCACHED = 'Memcached';
    const DRIVER_APC       = 'APC';
    const DRIVER_XCACHE    = 'XCache';

    const PORT_REDIS     = 6379;
    const PORT_MEMCACHED = 11211;

    private $config = null;
    private $driver = null;
    private $prefix = null;

    /**
     * Cache constructor.
     *
     * @param null $config
     */
    public function __construct($config = null)
    {
        $this->config = array();
        if (is_array($config)) {
            $this->config = $config;
        }
        $this->prefix = isset($this->config['prefix']) ? strval($this->config['prefix']) : '';
    }

    /**
     * Get Key
     *
     * @param $key
     *
     * @return string
     */
    private function getKey($key)
    {
        return $this->prefix . $key;
    }

    /**
     * Is Driver
     *
     * @param $driver
     *
     * @return bool
     */
    private function isDriver($driver)
    {
        if ($this->config['driver'] == $driver) {
            return true;
        }
        return false;
    }

    /**
     * Start Driver
     */
    private function startDriver()
    {
        if ($this->driver == null) {
            if ($this->isDriver(self::DRIVER_REDIS)) {
                $this->driver = new Redis();
                $this->driver->connect($this->config['server'], $this->config['port']);
                if (isset($this->config['password'])) {
                    $this->driver->auth($this->config['password']);
                }
                if (isset($this->config['database'])) {
                    $this->driver->select($this->config['database']);
                }
            }
        }
    }

    /**
     * Close Driver
     * @return null
     */
    public function closeDriver()
    {
        if ($this->isDriver(self::DRIVER_REDIS)) {
            return $this->driver->close();
        }
        return false;
    }

    /**
     * Get Driver
     * @return null
     */
    public function getDriver()
    {
        $this->startDriver();
        return $this->driver;
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
    public function getData($key, $timeout, $function)
    {
        $key = $this->getKey($key);
        if ($this->isExist($key)) {
            return unserialize($this->readValue($key));
        }
        $value = $function();
        $timeout = intval($timeout);
        $this->writeValue($key, serialize($value), $timeout);
        return $value;
    }

    /**
     * Is Exist
     *
     * @param $key
     *
     * @return bool
     */
    public function isExist($key)
    {
        $key = $this->getKey($key);
        $this->startDriver();
        if ($this->isDriver(self::DRIVER_REDIS)) {
            return $this->driver->exists($key);
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
    public function delete($key)
    {
        $key = $this->getKey($key);
        $this->startDriver();
        if ($this->isDriver(self::DRIVER_REDIS)) {
            $result = $this->driver->delete($key);
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
    public function writeValue($key, $value, $timeout = 0)
    {
        $key = $this->getKey($key);
        $this->startDriver();
        $timeout = intval($timeout);
        if ($this->isDriver(self::DRIVER_REDIS)) {
            $this->driver->set($key, $value, $timeout);
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
    public function readValue($key, $default = null)
    {
        $this->startDriver();
        if ($this->isDriver(self::DRIVER_REDIS)) {
            if (!$this->isExist($key)) {
                return $default;
            }
            $key = $this->getKey($key);
            return $this->driver->get($key);
        }
        return $default;
    }
}