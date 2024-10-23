<?php

namespace Yapp\GuzzleHttp\Interceptors\Rules;

use Psr\Http\Message\RequestInterface;
use Yapp\GuzzleHttp\Interceptors\InterceptorContextInterface;

interface RuleInterface
{
    /**
     * @param RequestInterface $request
     * @param InterceptorContextInterface $interceptorContext
     * @return bool
     */
    function tryMatch(RequestInterface $request, InterceptorContextInterface &$interceptorContext): bool;
}