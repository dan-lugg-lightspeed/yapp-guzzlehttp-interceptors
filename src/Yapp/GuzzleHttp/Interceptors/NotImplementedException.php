<?php

namespace Yapp\GuzzleHttp\Interceptors;

use Exception;

class NotImplementedException extends Exception
{
    public function __construct()
    {
        parent::__construct("Not yet implemented");
    }
}