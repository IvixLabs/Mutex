<?php
namespace IvixLabs\Mutex\Storage;

use IvixLabs\Mutex\MutexStorageInterface;

class RedisMutexStorage implements MutexStorageInterface
{
    const NAME = 'redis';

    private $host;
    private $port;

    public function __construct(array $settings)
    {
        $this->host = $settings['host'];
        $this->port = $settings['port'];
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
                $redis->psetex($name, $expire * 1000, $value);
            }

            return true;
        }

        return false;
    }

    public function isExists($name)
    {
        $redis = $this->getRedis();
        return $redis->exists($name);
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
