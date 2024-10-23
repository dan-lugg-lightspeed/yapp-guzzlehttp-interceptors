<?php

namespace Yapp\GuzzleHttp\Interceptors;

use Psr\Http\Message\RequestInterface;
use Yapp\GuzzleHttp\Interceptors\Rules\RuleInterface;

interface InterceptorInterface
{
    /**
     * @return array|RuleInterface[]
     */
    public function getInterceptorRules(): array;

    /**
     * @param RequestInterface $request
     * @param callable|null $middlewareCallable
     * @return bool
     */
    public function tryCreateMiddlewareCallable(RequestInterface $request, ?callable &$middlewareCallable = null): bool;
}