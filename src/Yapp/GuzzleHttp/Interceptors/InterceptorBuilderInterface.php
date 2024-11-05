<?php

namespace Yapp\GuzzleHttp\Interceptors;

interface InterceptorBuilderInterface
{
    /**
     * @param string|null $method
     * @return $this
     */
    public function withMethod(?string $method): static;

    /**
     * @param callable $requestTransformer
     * @return $this
     */
    public function withRequestTransformer(callable $requestTransformer): static;

    /**
     * @param callable $responseTransformer
     * @return $this
     */
    public function withResponseTransformer(callable $responseTransformer): static;

    /**
     * @param string $pathPattern
     * @return $this
     */
    public function withPathPattern(string $pathPattern): static;

    /**
     * @param InterceptorFactoryInterface $interceptorFactory
     * @return InterceptorInterface
     */
    public function build(InterceptorFactoryInterface $interceptorFactory): InterceptorInterface;
}