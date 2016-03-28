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
        @trigger_error('empty_error');
        $result = @$memcache->add($name, $value, 0, $expire);
        $this->handleLastError();
        return $result;
    }

    /**
     * @param $name
     * @return bool
     */
    public function delete($name)
    {
        $memcache = $this->getMemcache();
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
        $memcache = $this->getMemcache();
        return $memcache->get($name) !== false;
    }

}
