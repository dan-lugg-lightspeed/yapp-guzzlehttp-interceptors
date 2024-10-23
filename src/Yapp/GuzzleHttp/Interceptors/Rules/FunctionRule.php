<?php

namespace Yapp\GuzzleHttp\Interceptors\Rules;

use Psr\Http\Message\RequestInterface;
use Yapp\GuzzleHttp\Interceptors\InterceptorContextInterface;
use Yapp\GuzzleHttp\Interceptors\NotImplementedException;

class FunctionRule implements RuleInterface
{
    /**
     * @param RequestInterface $request
     * @param InterceptorContextInterface $interceptorContext
     * @return bool
     * @throws NotImplementedException
     */
    function tryMatch(RequestInterface $request, InterceptorContextInterface &$interceptorContext): bool
    {
        throw new NotImplementedException();
    }
}