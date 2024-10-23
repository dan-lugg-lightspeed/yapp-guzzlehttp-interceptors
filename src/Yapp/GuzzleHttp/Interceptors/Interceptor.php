<?php

namespace Yapp\GuzzleHttp\Interceptors;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;
use Yapp\GuzzleHttp\Interceptors\Rules\RuleInterface;

class Interceptor implements InterceptorInterface
{
    public const string INCOMING_HANDLER = "incoming";

    public const string OUTGOING_HANDLER = "outgoing";

    /**
     * @var array|RuleInterface[]
     */
    private array $rules;

    /**
     * @var Closure|null
     */
    private ?Closure $incomingHandler;

    /**
     * @var Closure|null
     */
    private ?Closure $outgoingHandler;

    /**
     * @param array|RuleInterface[] $rules
     * @param callable(RequestInterface):RequestInterface|null $incomingHandler
     * @param callable(ResponseInterface):ResponseInterface|null $outgoingHandler
     */
    public function __construct(array $rules, ?callable $incomingHandler = null, ?callable $outgoingHandler = null)
    {
        $this->rules = $rules;

        $this->incomingHandler = $incomingHandler
            ? $incomingHandler(...)
            : null;

        $this->outgoingHandler = $outgoingHandler
            ? $outgoingHandler(...)
            : null;
    }

    /**
     * @return array|RuleInterface[]
     */
    public function getInterceptorRules(): array
    {
        return $this->rules;
    }

    /**
     * @param RequestInterface $request
     * @param callable(callable):callable|null $middlewareCallable
     * @return bool
     */
    public function tryCreateMiddlewareCallable(RequestInterface $request, ?callable &$middlewareCallable = null): bool
    {
        $context = InterceptorContext::create();

        try {
            foreach ($this->rules as $rule) {
                if ($rule->tryMatch($request, $context)) {
                    continue;
                }

                return false;
            }

            $middlewareCallable = $this->createMiddlewareCallable($context);
            return true;
        }

        catch (Throwable $exception) {
            throw new RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param InterceptorContext $context
     * @return callable(callable):callable
     */
    public function createMiddlewareCallable(InterceptorContext $context): callable
    {
        $incomingHandler = $this->incomingHandler ?? fn(RequestInterface $request) => $request;
        $outgoingHandler = $this->outgoingHandler ?? fn(ResponseInterface $response) => $response;

        return function (callable $function) use ($incomingHandler, $outgoingHandler, $context): callable {
            return function (RequestInterface $request, array $options) use ($function, $incomingHandler, $outgoingHandler, $context) {
                $options = array_merge($options, [
                    "context" => $context,
                ]);

                $request = $incomingHandler($request, $options);
                $promise = $function($request, $options);

                return $promise->then(function (ResponseInterface $response) use ($options, $outgoingHandler) {
                    return $outgoingHandler($response, $options);
                });
            };
        };
    }

}