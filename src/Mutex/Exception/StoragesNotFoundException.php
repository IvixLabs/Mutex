<?php
namespace IvixLabs\Mutex\Exception;


class StorageNotFoundMutexException extends AbstractMutexException
{


    /**
     * StorageNotFoundMutexException constructor.
     */
    public function __construct($name)
    {
        $this->message = 'Storage with name "' . $name . '" not found"';
    }
}
