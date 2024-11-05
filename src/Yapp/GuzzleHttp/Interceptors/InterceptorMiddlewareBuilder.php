<?php

namespace Yapp\GuzzleHttp\Interceptors;

use Exception;
use Throwable;
use Yapp\GuzzleHttp\Interceptors\Rules\PathPartMatcherParser;
use Yapp\GuzzleHttp\Interceptors\Rules\PathRuleParser;

class InterceptorMiddlewareBuilder implements InterceptorMiddlewareBuilderInterface
{
    /**
     * @param callable $middlewareBuilderBlock
     * @return callable
     * @throws InterceptorMiddlewareBuildingException
     */
    public static function buildWith(callable $middlewareBuilderBlock): callable
    {
        try {
            $pathPartMatcherParser = new PathPartMatcherParser();
            $pathRuleParser = new PathRuleParser($pathPartMatcherParser);
            $interceptorFactory = new InterceptorFactory($pathRuleParser);
            $middlewareFactoryBuilder = new InterceptorMiddlewareBuilder($interceptorFactory);
            $middlewareBuilderBlock($middlewareFactoryBuilder);
            return $middlewareFactoryBuilder->build();
        }

        catch (Exception $exception) {
            $message = "Failed to build middleware factory";
            throw new InterceptorMiddlewareBuildingException($message, 0, $exception);
        }
    }

    /**
     * @var array|InterceptorBuilderInterface[]
     */
    private array $interceptorBuilders = [];

    /**
     * @var InterceptorFactoryInterface
     */
    private InterceptorFactoryInterface $interceptorFactory;

    /**
     * @param InterceptorFactoryInterface $interceptorFactory
     */
    public function __construct(InterceptorFactoryInterface $interceptorFactory)
    {
        $this->interceptorFactory = $interceptorFactory;
    }

    /**
     * @param string|null $method
     * @param string $pathPattern
     * @return InterceptorBuilderInterface
     * @throws InterceptorMiddlewareBuildingException
     */
    public function intercept(?string $method, string $pathPattern): InterceptorBuilderInterface
    {
        try {
            $interceptorBuilder = (new InterceptorBuilder())
                ->withMethod($method)
                ->withPathPattern($pathPattern);

            $this->interceptorBuilders[] = $interceptorBuilder;
            return $interceptorBuilder;
        }

        catch (Throwable $exception) {
            $message = vsprintf("Failed to build interceptor where method is `%s` and path pattern is `%s`", [
                $method,
                $pathPattern,
            ]);

            throw new InterceptorMiddlewareBuildingException($message, 0, $exception);
        }
    }

    /**
     * @param string $pathPattern
     * @return InterceptorBuilderInterface
     * @throws InterceptorMiddlewareBuildingException
     */
    public function any(string $pathPattern): InterceptorBuilderInterface
    {
        return $this->intercept(null, $pathPattern);
    }

    /**
     * @param string $pathPattern
     * @return InterceptorBuilderInterface
     * @throws InterceptorMiddlewareBuildingException
     */
    public function get(string $pathPattern): InterceptorBuilderInterface
    {
        return $this->intercept(HttpMethod::GET, $pathPattern);
    }

    /**
     * @param string $pathPattern
     * @return InterceptorBuilderInterface
     * @throws InterceptorMiddlewareBuildingException
     */
    public function put(string $pathPattern): InterceptorBuilderInterface
    {
        return $this->intercept(HttpMethod::PUT, $pathPattern);
    }

    /**
     * @param string $pathPattern
     * @return InterceptorBuilderInterface
     * @throws InterceptorMiddlewareBuildingException
     */
    public function post(string $pathPattern): InterceptorBuilderInterface
    {
        return $this->intercept(HttpMethod::POST, $pathPattern);
    }

    /**
     * @param string $pathPattern
     * @return InterceptorBuilderInterface
     * @throws InterceptorMiddlewareBuildingException
     */
    public function patch(string $pathPattern): InterceptorBuilderInterface
    {
        return $this->intercept(HttpMethod::PATCH, $pathPattern);
    }

    /**
     * @param string $pathPattern
     * @return InterceptorBuilderInterface
     * @throws InterceptorMiddlewareBuildingException
     */
    public function delete(string $pathPattern): InterceptorBuilderInterface
    {
        return $this->intercept(HttpMethod::DELETE, $pathPattern);
    }

    /**
     * @return InterceptorMiddlewareInterface
     */
    public function build(): InterceptorMiddlewareInterface
    {
        $interceptors = [];

        foreach ($this->interceptorBuilders as $interceptorBuilder) {
            $interceptors[] = $interceptorBuilder->build($this->interceptorFactory);
        }

        return new InterceptorMiddleware($interceptors);
    }
}