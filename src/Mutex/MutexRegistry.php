<?php
namespace IvixLabs\Mutex;

use IvixLabs\Mutex\Exception\ExistsMutexException;
use IvixLabs\Mutex\Exception\FreeMutexException;
use IvixLabs\Mutex\Exception\NotFoundMutexException;

class MutexRegistry
{
    protected static $instance;

    /**
     * @var Mutex[]
     */
    private $mutexes;

    /**
     * MutexRegistry constructor.
     */
    protected function __construct()
    {
        $this->mutexes = array();
    }

    /**
     * @param string $key
     * @param Mutex $mutex
     */
    protected function doAdd($key, Mutex $mutex)
    {
        if (isset($this->mutexes[$key])) {
            throw new ExistsMutexException($key);
        }

        $this->mutexes[$key] = $mutex;
    }

    /**
     * @param $key
     * @return Mutex
     */
    protected function doGet($key)
    {
        if (isset($this->mutexes[$key])) {
            return $this->mutexes[$key];
        }

        return null;
    }

    /**
     * @param $key
     */
    protected function doRemove($key)
    {
        if (isset($this->mutexes[$key])) {
            unset($this->mutexes[$key]);
        } else {
            throw new NotFoundMutexException($key);
        }
    }

    /**
     * @return MutexRegistry
     */
    public final static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }

        return self::$instance;
    }


    /**
     * @param string $key
     * @param Mutex $mutex
     */
    public static function add($key, Mutex $mutex)
    {
        self::getInstance()->doAdd($key, $mutex);
    }

    /**
     * @param $key
     * @return Mutex
     */
    public static function get($key)
    {
        return self::getInstance()->doGet($key);
    }

    /**
     * @param $key
     * @param Mutex $mutex
     */
    public static function remove($key, Mutex $mutex)
    {
        if (!$mutex->isUnlocked()) {
            throw new FreeMutexException();
        }

        self::getInstance()->doRemove($key);
    }

    function __destruct()
    {
        foreach ($this->mutexes as $key => $mutex) {
            if (!$mutex->isUnlocked()) {
                throw new FreeMutexException($key);
            }
        }
    }


}
