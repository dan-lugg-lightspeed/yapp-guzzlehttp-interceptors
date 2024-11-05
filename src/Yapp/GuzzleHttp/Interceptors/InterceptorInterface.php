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
     * @param callable|null $requestTransformer
     * @param callable|null $responseTransformer
     * @return bool
     */
    public function tryCreateTransformers(RequestInterface $request, ?callable &$requestTransformer = null, ?callable &$responseTransformer = null): bool;
}