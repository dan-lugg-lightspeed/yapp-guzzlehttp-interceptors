<?php

namespace Yapp\GuzzleHttp\Interceptors;

interface MiddlewareFactoryBuilderInterface
{
    /**
     * @return MiddlewareFactoryInterface
     */
    public function build(): MiddlewareFactoryInterface;
}