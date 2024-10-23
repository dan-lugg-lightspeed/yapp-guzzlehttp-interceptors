<?php

namespace Yapp\GuzzleHttp\Interceptors;

interface InterceptorFactoryInterface
{
    /**
     * @param string|null $method
     * @param string $pathPattern
     * @param callable|null $incomingHandler
     * @param callable|null $outgoingHandler
     * @return InterceptorInterface
     */
    public function createInterceptor(?string $method, string $pathPattern, ?callable $incomingHandler = null, ?callable $outgoingHandler = null): InterceptorInterface;
}