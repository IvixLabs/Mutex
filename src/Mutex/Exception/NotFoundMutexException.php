<?php
namespace IvixLabs\Mutex\Exception;


class NotFoundMutexException extends AbstractMutexException
{


    /**
     * ExpiredMutexException constructor.
     */
    public function __construct($key)
    {
        $this->message = 'Mutex with key "' . $key . '" not found';
    }
}
