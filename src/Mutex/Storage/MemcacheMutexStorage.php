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
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->host = $settings['host'];
        $this->port = $settings['port'];
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
        $memcache = $this->getMemcache();
        set_error_handler(function () {
            return true;
        });
        $result = @$memcache->add($name, $value, 0, $expire);
        restore_error_handler();
        return $result;
    }

    /**
     * @param $name
     * @return bool
     */
    public function delete($name)
    {
        $memcache = $this->getMemcache();
        set_error_handler(function () {
            return true;
        });
        $result = @$memcache->delete($name);
        restore_error_handler();
        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    public function isExists($name)
    {
        $memcache = $this->getMemcache();
        return $memcache->get($name) !== false;
    }

}
