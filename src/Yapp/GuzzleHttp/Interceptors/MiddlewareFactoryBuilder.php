<?php

namespace Yapp\GuzzleHttp\Interceptors;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Yapp\GuzzleHttp\Interceptors\Rules\PathPartMatcherParser;
use Yapp\GuzzleHttp\Interceptors\Rules\PathRuleParser;

class MiddlewareFactoryBuilder implements MiddlewareFactoryBuilderInterface
{
    /**
     * @param callable(MiddlewareFactoryBuilder):void $middlewareFactoryBuilderBlock
     * @return MiddlewareFactoryInterface
     * @throws MiddlewareFactoryBuildingException
     */
    public static function buildWith(callable $middlewareFactoryBuilderBlock): MiddlewareFactoryInterface
    {
        try {
            $pathPartMatcherParser = new PathPartMatcherParser();
            $pathRuleParser = new PathRuleParser($pathPartMatcherParser);
            $interceptorFactory = new InterceptorFactory($pathRuleParser);
            $middlewareFactoryBuilder = new MiddlewareFactoryBuilder($interceptorFactory);
            $middlewareFactoryBuilderBlock($middlewareFactoryBuilder);
            return $middlewareFactoryBuilder->build();
        }

        catch (Exception $exception) {
            $message = "Failed to build middleware factory";
            throw new MiddlewareFactoryBuildingException($message, 0, $exception);
        }
    }

    /**
     * @var array|InterceptorInterface[]
     */
    private array $interceptors = [];

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
     * @param array{incoming: callable(RequestInterface):RequestInterface, outgoing: callable(ResponseInterface):ResponseInterface} $handlers
     * @return $this
     * @throws MiddlewareFactoryBuildingException
     */
    public function intercept(?string $method, string $pathPattern, array $handlers): static
    {
        try {
            $incomingHandler = $handlers["incoming"] ?? null;
            $outgoingHandler = $handlers["outgoing"] ?? null;
            $this->interceptors[] = $this->interceptorFactory->createInterceptor($method, $pathPattern, $incomingHandler, $outgoingHandler);
            return $this;
        }

        catch (Exception $exception) {
            $message = vsprintf("Failed to build interceptor where method is `%s` and path pattern is `%s`", [
                $method,
                $pathPattern,
            ]);

            throw new MiddlewareFactoryBuildingException($message, 0, $exception);
        }
    }

    /**
     * @param string $pathPattern
     * @param array{incoming: callable(RequestInterface):RequestInterface, outgoing: callable(ResponseInterface):ResponseInterface} $handlers
     * @return $this
     * @throws MiddlewareFactoryBuildingException
     */
    public function any(string $pathPattern, array $handlers): static
    {
        return $this->intercept(null, $pathPattern, $handlers);
    }

    /**
     * @param string $pathPattern
     * @param array{incoming: callable(RequestInterface):RequestInterface, outgoing: callable(ResponseInterface):ResponseInterface} $handlers
     * @return $this
     * @throws MiddlewareFactoryBuildingException
     */
    public function get(string $pathPattern, array $handlers): static
    {
        return $this->intercept(HttpMethod::GET, $pathPattern, $handlers);
    }

    /**
     * @param string $pathPattern
     * @param array{incoming: callable(RequestInterface):RequestInterface, outgoing: callable(ResponseInterface):ResponseInterface} $handlers
     * @return $this
     * @throws MiddlewareFactoryBuildingException
     */
    public function put(string $pathPattern, array $handlers): static
    {
        return $this->intercept(HttpMethod::PUT, $pathPattern, $handlers);
    }

    /**
     * @param string $pathPattern
     * @param array{incoming: callable(RequestInterface):RequestInterface, outgoing: callable(ResponseInterface):ResponseInterface} $handlers
     * @return $this
     * @throws MiddlewareFactoryBuildingException
     */
    public function post(string $pathPattern, array $handlers): static
    {
        return $this->intercept(HttpMethod::POST, $pathPattern, $handlers);
    }

    /**
     * @param string $pathPattern
     * @param array{incoming: callable(RequestInterface):RequestInterface, outgoing: callable(ResponseInterface):ResponseInterface} $handlers
     * @return $this
     * @throws MiddlewareFactoryBuildingException
     */
    public function patch(string $pathPattern, array $handlers): static
    {
        return $this->intercept(HttpMethod::PATCH, $pathPattern, $handlers);
    }

    /**
     * @param string $pathPattern
     * @param array{incoming: callable(RequestInterface):RequestInterface, outgoing: callable(ResponseInterface):ResponseInterface} $handlers
     * @return $this
     * @throws MiddlewareFactoryBuildingException
     */
    public function delete(string $pathPattern, array $handlers): static
    {
        return $this->intercept(HttpMethod::DELETE, $pathPattern, $handlers);
    }

    /**
     * @return MiddlewareFactoryInterface
     */
    public function build(): MiddlewareFactoryInterface
    {
        return new MiddlewareFactory($this->interceptors);
    }
}