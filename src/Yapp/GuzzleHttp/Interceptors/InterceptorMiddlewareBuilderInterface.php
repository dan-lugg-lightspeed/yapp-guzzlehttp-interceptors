<?php

namespace Yapp\GuzzleHttp\Interceptors;

interface InterceptorMiddlewareBuilderInterface
{
    /**
     * @return InterceptorMiddlewareInterface
     */
    public function build(): InterceptorMiddlewareInterface;
}