<?php

namespace Yapp\GuzzleHttp\Interceptors;

interface InterceptorMiddlewareInterface
{
    public function __invoke(callable $handler): callable;
}