<?php

namespace Yapp\GuzzleHttp\Interceptors;

use Psr\Http\Message\RequestInterface;

class InterceptorMiddleware implements InterceptorMiddlewareInterface
{
    /**
     * @var array|InterceptorInterface[]
     */
    private array $interceptors;

    /**
     * @param array|InterceptorInterface[] $interceptors
     */
    public function __construct(array $interceptors = [])
    {
        $this->interceptors = $interceptors;
    }

    /**
     * @param callable $handler
     * @return callable
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $requestTransformers = [];
            $responseTransformers = [];
            $this->aggregateTransformers($request, $requestTransformers, $responseTransformers);

            foreach ($requestTransformers as $requestTransformer) {
                $request = $requestTransformer($request, $options);
            }

            $promise = $handler($request, $options);

            foreach (array_reverse($responseTransformers) as $responseTransformer) {
                $promise = $promise->then($responseTransformer);
            }

            return $promise;
        };
    }

    /**
     * @param RequestInterface $request
     * @param array|callable[] $requestTransformers
     * @param array|callable[] $responseTransformers
     * @return void
     */
    private function aggregateTransformers(RequestInterface $request, array &$requestTransformers = [], array &$responseTransformers = []): void
    {
        foreach ($this->interceptors as $interceptor) {
            $requestTransformer = null;
            $responseTransformer = null;

            if ($interceptor->tryCreateTransformers($request, $requestTransformer, $responseTransformer)) {
                $requestTransformers[] = $requestTransformer;
                $responseTransformers[] = $responseTransformer;
            }
        }
    }
}