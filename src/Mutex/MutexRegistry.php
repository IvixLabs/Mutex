<?php
namespace IvixLabs\Mutex;

use IvixLabs\Mutex\Exception\ExistsMutexException;
use IvixLabs\Mutex\Exception\FreeMutexException;
use IvixLabs\Mutex\Exception\NotFoundMutexException;

class MutexRegistry
{
    /**
     * @var Mutex[]
     */
    private $mutexes = array();

    /**
     * @param string $key
     * @param Mutex $mutex
     */
    public function add($key, Mutex $mutex)
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
    public function get($key)
    {
        if (isset($this->mutexes[$key])) {
            return $this->mutexes[$key];
        }

        return null;
    }

    /**
     * @param $key
     * @param Mutex $mutex
     */
    public function remove($key, Mutex $mutex)
    {
        if (!$mutex->isUnlocked()) {
            throw new FreeMutexException($key);
        }

        if (isset($this->mutexes[$key])) {
            unset($this->mutexes[$key]);
        } else {
            throw new NotFoundMutexException($key);
        }
    }

    function __destruct()
    {
        foreach ($this->mutexes as $key => $mutex) {
            if(!$mutex->isIgnoreDestructException()) {
                if (!$mutex->isUnlocked()) {
                    throw new FreeMutexException($key);
                }
            }
        }
    }
}
