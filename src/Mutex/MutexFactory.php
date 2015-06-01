<?php
namespace IvixLabs\Mutex;

use IvixLabs\Mutex\Exception\NoStorageMutexException;
use IvixLabs\Mutex\Exception\StorageExistsMutexException;
use IvixLabs\Mutex\Exception\StorageNotFoundMutexException;
use IvixLabs\Mutex\Exception\WrongNameMutexException;

class MutexFactory
{
    /**
     * @var MutexStorageInterface[]
     */
    private $storages = array();


    /**
     * @param string $key
     * @param string $storageName
     * @return Mutex
     */
    public function create($key, $storageName = null)
    {
        if (preg_match('/[^0-9a-zA-z_]/um', $key)) {
            throw new WrongNameMutexException();
        }

        $mutex = MutexRegistry::get($key);
        if ($mutex === null) {
            if ($storageName === null) {
                if (empty($this->storages)) {
                    throw new NoStorageMutexException();
                }
                $storage = reset($this->storages);
            } else {
                if (!isset($this->storages[$storageName])) {
                    throw new StorageNotFoundMutexException($storageName);
                }
                $storage = $this->storages[$storageName];
            }

            $mutex = new Mutex($key, $storage);
            MutexRegistry::add($key, $mutex);
        }

        return $mutex;
    }

    /**
     * @param MutexStorageInterface $storage
     */
    public function addStorage(MutexStorageInterface $storage)
    {
        $name = $storage->getName();

        if (isset($this->storages[$name])) {
            throw new StorageExistsMutexException();
        }

        $this->storages[$name] = $storage;
    }


}
