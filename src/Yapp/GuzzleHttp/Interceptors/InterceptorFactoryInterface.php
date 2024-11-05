<?php

namespace Yapp\GuzzleHttp\Interceptors;

interface InterceptorFactoryInterface
{
    /**
     * @param string|null $method
     * @param string $pathPattern
     * @param callable|null $requestTransformer
     * @param callable|null $responseTransformer
     * @return InterceptorInterface
     */
    public function createInterceptor(?string $method, string $pathPattern, ?callable $requestTransformer = null, ?callable $responseTransformer = null): InterceptorInterface;
}