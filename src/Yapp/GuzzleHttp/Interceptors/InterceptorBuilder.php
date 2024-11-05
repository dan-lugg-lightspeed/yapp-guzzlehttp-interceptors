<?php

namespace Yapp\GuzzleHttp\Interceptors;

use Closure;

class InterceptorBuilder implements InterceptorBuilderInterface
{
    /**
     * @var string|null
     */
    private ?string $method = null;

    /**
     * @var string|null
     */
    private ?string $pathPattern = null;

    /**
     * @var Closure|null
     */
    private ?Closure $requestTransformer = null;

    /**
     * @var Closure|null
     */
    private ?Closure $responseTransformer = null;

    /**
     * @param string|null $method
     * @return $this
     */
    public function withMethod(?string $method): static
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param string $pathPattern
     * @return $this
     */
    public function withPathPattern(string $pathPattern): static
    {
        $this->pathPattern = $pathPattern;
        return $this;
    }

    /**
     * @param callable $requestTransformer
     * @return $this
     */
    public function withRequestTransformer(callable $requestTransformer): static
    {
        $this->requestTransformer = $requestTransformer(...);
        return $this;
    }

    /**
     * @param callable $responseTransformer
     * @return $this
     */
    public function withResponseTransformer(callable $responseTransformer): static
    {
        $this->responseTransformer = $responseTransformer(...);
        return $this;
    }

    /**
     * @param InterceptorFactoryInterface $interceptorFactory
     * @return InterceptorInterface
     */
    public function build(InterceptorFactoryInterface $interceptorFactory): InterceptorInterface
    {
        return $interceptorFactory->createInterceptor($this->method, $this->pathPattern, $this->requestTransformer, $this->responseTransformer);
    }
}