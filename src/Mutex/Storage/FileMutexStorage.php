<?php
namespace IvixLabs\Mutex\Storage;

use IvixLabs\Mutex\MutexStorageInterface;

class FileMutexStorage implements MutexStorageInterface
{
    const NAME = 'file';

    private $dir;
    private $locks = [];

    /**
     * FileMutexStorage constructor.
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->dir = $settings['dir'];
    }

    /**
     * @param string $name
     * @param string $value
     * @param int $expire
     * @return bool
     */
    public function add($name, $value, $expire = null)
    {
        if (isset($this->locks[$name])) {
            return true;
        }

        $handler = $this->getFileHandler($name);
        $result = flock($handler, LOCK_EX | LOCK_NB);
        if ($result) {
            ftruncate($handler, 0);
            $this->locks[$name] = $handler;
            $expireTime = time() + $expire;
            fwrite($handler, $expireTime);
        }
        return $result;
    }

    /**
     * @param $name
     * @return bool
     */
    public function delete($name)
    {
        if (!isset($this->locks[$name])) {
            return false;
        }
        $handler = $this->locks[$name];
        $now = time();
        rewind($handler);
        $expireTime = fread($handler, 20);
        $result = flock($handler, LOCK_UN);
        return $result && ($now < $expireTime);
    }

    /**
     * @param string $name
     * @return resource
     */
    private function getFileHandler($name)
    {
        $path = $this->getFilePath($name);
        $handler = fopen($path, 'a+');
        if ($handler === false) {
            throw new \RuntimeException('Can not open file ' . $path);
        }

        return $handler;
    }

    /**
     * @param string $name
     * @return string
     */
    private function getFilePath($name)
    {
        return $this->dir . DIRECTORY_SEPARATOR . $name;
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
        $handler = $this->getFileHandler($name);
        $result = flock($handler, LOCK_EX | LOCK_NB);
        fclose($handler);
        return !$result;
    }

}
