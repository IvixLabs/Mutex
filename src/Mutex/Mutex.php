<?php
namespace IvixLabs\Mutex;

use IvixLabs\Mutex\Exception\FreeStateMutexException;
use IvixLabs\Mutex\Exception\MaxTriesMutexException;
use IvixLabs\Mutex\Exception\ExpiredMutexException;
use IvixLabs\Mutex\Exception\UnlockMutexException;

class Mutex
{
    private $level = 0;

    private $free = false;

    private $key;

    /**
     * @var MutexStorageInterface
     */
    private $storage;


    function __construct($key, MutexStorageInterface $storage)
    {
        $this->key = $key;
        $this->storage = $storage;
    }

    /**
     * @param int $expire
     * @param int $maxTries
     * @param int $usleep
     * @return int
     */
    public function lock($expire = 5, $maxTries = 10, $usleep = 100000)
    {
        $this->checkFreeState();

        if ($this->level > 0) {
            $this->level++;
            return 0;
        }

        $attempts = 0;
        while (!$this->storage->add($this->key, true, $expire)) {
            if ($attempts++ > $maxTries) {
                throw new MaxTriesMutexException();
            };
            usleep($usleep);
        };

        $this->level = 1;

        return $attempts;
    }

    public function unlock()
    {
        $this->checkFreeState();

        if ($this->level === 1) {
            if (!$this->storage->delete($this->key)) {
                throw new ExpiredMutexException();
            }
        } else {
            if ($this->isUnlocked()) {
                throw new UnlockMutexException();
            }
        }
        $this->level--;
    }

    public function isUnlocked()
    {
        return $this->level < 1;
    }

    /**
     * @return boolean
     */
    public function isFree()
    {
        return $this->free;
    }

    public function free()
    {
        $this->checkFreeState();
        MutexRegistry::remove($this->key, $this);
        $this->free = true;
    }

    private function checkFreeState()
    {
        if ($this->free) {
            throw new FreeStateMutexException();
        }
    }
}
