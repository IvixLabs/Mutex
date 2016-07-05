<?php
namespace IvixLabs\Mutex\Storage;

use IvixLabs\Mutex\MutexStorageInterface;

class MemcachedMutexStorage implements MutexStorageInterface
{
    const NAME = 'memcached';

    protected $host;
    protected $port;
    private $memcached;

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
     * @return \Memcached
     */
    protected function getMemcached()
    {
        if ($this->memcached === null) {
            $this->memcached = new \Memcached();
            $this->memcached->addServer($this->host, $this->port);
        }

        return $this->memcached;
    }

    /**
     * @param string $name
     * @param string $value
     * @param int $expire
     * @return bool
     */
    public function add($name, $value, $expire = null)
    {
        $memcache = $this->getMemcached();
        set_error_handler(function () {
            return true;
        });
        $result = @$memcache->add($name, $value, $expire);
        restore_error_handler();
        return $result;
    }

    /**
     * @param $name
     * @return bool
     */
    public function delete($name)
    {
        $memcache = $this->getMemcached();
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
        $memcache = $this->getMemcached();
        if ($memcache->get($name) === false) {
            return $memcache->getResultCode() !== \Memcached::RES_NOTFOUND;
        }

        return true;
    }
}
