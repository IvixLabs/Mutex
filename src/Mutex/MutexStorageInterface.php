<?php
namespace IvixLabs\Mutex;


interface MutexStorageInterface
{

    /**
     * @param string $name
     * @param string $value
     * @param int $expire
     * @return bool
     */
    public function add($name, $value, $expire = null);

    /**
     * @param $name
     * @return bool
     */
    public function delete($name);

    /**
     * @return string
     */
    public function getName();
}
