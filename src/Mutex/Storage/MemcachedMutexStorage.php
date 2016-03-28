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
        @trigger_error('empty_error');
        $result = $memcache->add($name, $value, $expire);
        $this->handleLastError();
        return $result;
    }

    /**
     * @param $name
     * @return bool
     */
    public function delete($name)
    {
        $memcache = $this->getMemcached();
        @trigger_error('empty_error');
        $result = @$memcache->delete($name);
        $this->handleLastError();
        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    private function handleLastError()
    {
        $error = error_get_last();
        if ($error !== null && $error['message'] == 'empty_error') {
            throw new \RuntimeException(var_export($error, true));
        }
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
