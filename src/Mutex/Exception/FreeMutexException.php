<?php
namespace IvixLabs\Mutex\Exception;

class FreeMutexException extends AbstractMutexException
{


    /**
     * FreeMutexException constructor.
     */
    public function __construct($key)
    {
        $this->message = 'Impossible free locked mutex "' . $key . '"';
    }
}