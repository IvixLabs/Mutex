<?php
namespace IvixLabs\Mutex\Storage;

use IvixLabs\Mutex\MutexStorageInterface;

class MemcacheMutexStorage implements MutexStorageInterface
{
    const NAME = 'memcache';

    private $host;
    private $port;
    private $memcache;

    /**
     * MemcacheMutexStorage constructor.
     * @param $host
     * @param $port
     */
    public function __construct($host = '127.0.0.1', $port = 11211)
    {
        $this->host = $host;
        $this->port = $port;
    }


    /**
     * @return \Memcache
     */
    private function getMemcache()
    {
        if ($this->memcache === null) {
            $this->memcache = new \Memcache();
            $this->memcache->pconnect($this->host, $this->port);
        }

        return $this->memcache;
    }

    /**
     * @param string $name
     * @param string $value
     * @param int $expire
     * @return bool
     */
    public function add($name, $value, $expire = null)
    {
        return $this->getMemcache()->add($name, $value, 0, $expire);
    }

    /**
     * @param $name
     * @return bool
     */
    public function delete($name)
    {
        return $this->getMemcache()->delete($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }


}
