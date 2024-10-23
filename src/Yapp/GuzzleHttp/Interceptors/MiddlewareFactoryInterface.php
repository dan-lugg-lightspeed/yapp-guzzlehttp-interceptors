<?php

namespace Yapp\GuzzleHttp\Interceptors;

use Psr\Http\Message\RequestInterface;

interface MiddlewareFactoryInterface
{
    /**
     * @param RequestInterface $request
     * @return array|(callable(callable):callable)[]
     */
    public function getMiddlewareCallables(RequestInterface $request): array;
}