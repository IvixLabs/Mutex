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
        if ($expire !== null) {
            throw new \LogicException('expire param not supported');
        }

        if (isset($this->locks[$name])) {
            return true;
        }

        $handler = $this->getFileHandler($name);
        $result = flock($handler, LOCK_EX | LOCK_NB);
        if ($result) {
            $this->locks[$name] = $handler;
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
        $result = flock($handler, LOCK_UN);
        return $result;
    }

    /**
     * @param string $name
     * @return resource
     */
    private function getFileHandler($name)
    {
        $path = $this->getFilePath($name);
        $handler = fopen($path, 'w');
        if ($handler === false) {
            throw new \RuntimeException('Can open file ' . $path);
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
        throw new \RuntimeException('This operation not supported');
    }

}
