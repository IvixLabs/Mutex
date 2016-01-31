<?php
namespace IvixLabs\Mutex\Storage;

use IvixLabs\Mutex\MutexStorageInterface;

class MemcacheMutexStorage implements MutexStorageInterface
{
    const NAME = 'memcache';

    private $host;
    private $port;

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
    protected function getMemcache()
    {
        static $memcache;
        if ($memcache === null) {
            $memcache = new \Memcache();
            $memcache->pconnect($this->host, $this->port);
        }

        return $memcache;
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
        @trigger_error(null);
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
        $memcache = $this->getMemcache();
        @trigger_error(null);
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
        if ($error !== null && (!empty($error['message']) || $error['type'] !== E_USER_NOTICE)) {
            throw new \RuntimeException(var_export($error, true));
        }
    }

}
