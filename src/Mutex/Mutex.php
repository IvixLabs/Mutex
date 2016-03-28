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

    private $skipUnlockException = false;

    /**
     * @var MutexStorageInterface
     */
    private $storage;

    /**
     * @var MutexRegistry
     */
    private $registry;


    function __construct($key, MutexRegistry $registry, MutexStorageInterface $storage)
    {
        $this->key = $key;
        $this->storage = $storage;
        $this->registry = $registry;
    }

    /**
     * @param int $expire
     * @param int $maxTries
     * @param int $usleep
     * @return int
     */
    public function lock($expire, $maxTries = 10, $usleep = 100000)
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
                if(!$this->isSkipUnlockException()) {
                    $this->level = 0;
                    throw new ExpiredMutexException($this->key);
                }
            }
        } else {
            if ($this->isUnlocked()) {
                throw new UnlockMutexException($this->key);
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
        $this->registry->remove($this->key, $this);
        $this->free = true;
    }

    private function checkFreeState()
    {
        if ($this->free) {
            throw new FreeStateMutexException();
        }
    }

    public function isExists()
    {
        return $this->storage->isExists($this->key);
    }

    /**
     * @param $value
     */
    public function skipUnlockException($value) {
        $this->skipUnlockException = $value;
    }

    /**
     * @return boolean
     */
    public function isSkipUnlockException()
    {
        return $this->skipUnlockException;
    }
}
