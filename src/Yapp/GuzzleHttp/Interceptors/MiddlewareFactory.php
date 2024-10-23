<?php

namespace Yapp\GuzzleHttp\Interceptors;

use Exception;
use Psr\Http\Message\RequestInterface;
use UnexpectedValueException;

class MiddlewareFactory implements MiddlewareFactoryInterface
{
    /**
     * @var array|InterceptorInterface[]
     */
    private array $interceptors = [];

    /**
     * @param array|InterceptorInterface[] $interceptors
     */
    public function __construct(array $interceptors)
    {
        $this->interceptors = $interceptors;
    }

    /**
     * @param RequestInterface $request
     * @return array|(callable(callable):callable)[]
     * @throws MiddlewareCreationException
     */
    public function getMiddlewareCallables(RequestInterface $request): array
    {
        try {
            $middlewareCallables = [];

            foreach ($this->interceptors as $interceptor) {
                $middlewareCallable = null;

                if ($interceptor->tryCreateMiddlewareCallable($request, $middlewareCallable)) {
                    if (is_callable($middlewareCallable)) {
                        $middlewareCallables[] = $middlewareCallable;
                        continue;
                    }

                    throw new UnexpectedValueException("Middleware callable mismatch");
                }
            }

            return $middlewareCallables;
        }

        catch (Exception $exception) {
            $message = vsprintf("Failed to build middleware callables for request of `%s %s`", [
                $request->getMethod(),
                $request->getUri()->getPath(),
            ]);

            throw new MiddlewareCreationException($message, 0, $exception);
        }
    }
}
