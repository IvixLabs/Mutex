<?php
namespace IvixLabs\Mutex\Exception;


class ExistsMutexException extends AbstractMutexException
{


    /**
     * ExpiredMutexException constructor.
     */
    public function __construct($key)
    {
        $this->message = 'Mutex with key "' . $key . '" already exists';
    }
}
