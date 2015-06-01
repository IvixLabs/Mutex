<?php
namespace IvixLabs\Mutex\Exception;

class FreeStateMutexException extends AbstractMutexException
{


    /**
     * FreeMutexException constructor.
     */
    public function __construct()
    {
        $this->message = 'Mutex is freed';
    }
}