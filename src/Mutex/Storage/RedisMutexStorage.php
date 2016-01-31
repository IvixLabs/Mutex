<?php
namespace IvixLabs\Mutex\Storage;

use IvixLabs\Mutex\MutexStorageInterface;

class RedisMutexStorage implements MutexStorageInterface
{
    const NAME = 'redis';

    private $host;
    private $port;

    /**
     * RedisMutexStorage constructor.
     * @param $host
     * @param $port
     */
    public function __construct($host = '127.0.0.1', $port = 6379)
    {
        $this->host = $host;
        $this->port = $port;
    }


    /**
     * @return \Redis
     */
    protected function getRedis()
    {
        static $redis;
        if ($redis === null) {
            $redis = new \Redis();
            $redis->pconnect($this->host, $this->port);
        }

        return $redis;
    }

    /**
     * @param string $name
     * @param string $value
     * @param int $expire
     * @return bool
     */
    public function add($name, $value, $expire = null)
    {
        $redis = $this->getRedis();
        if ($redis->setnx($name, $value)) {
            if ($expire !== null) {
                $redis->set($name, $value, array('xx', 'px' => $expire));
            }

            return true;
        }

        return false;
    }

    /**
     * @param $name
     * @return bool
     */
    public function delete($name)
    {
        if (!$this->getRedis()->exists($name)) {
            return false;
        }

        $this->getRedis()->delete($name);

        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }


}
