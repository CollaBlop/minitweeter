<?php

namespace mf\auth\exception;



class AuthentificationException extends \Exception { 

    //Construit l'exception
    public function __construct($message, $code = 0, Throwable $previous = null) 
    {
        parent::__construct($message, $code, $previous);
    }

    //Transforme l'exception en string
    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n"; //Retourne l'exception
    }
}