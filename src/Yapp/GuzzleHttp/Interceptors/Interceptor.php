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
    /**
     * @var array|RuleInterface[]
     */
    private array $rules;

    /**
     * @var Closure|null
     */
    private ?Closure $requestTransformer;

    /**
     * @var Closure|null
     */
    private ?Closure $responseTransformer;

    /**
     * @param array|RuleInterface[] $rules
     * @param callable(RequestInterface):RequestInterface|null $requestTransformer
     * @param callable(ResponseInterface):ResponseInterface|null $responseTransformer
     */
    public function __construct(array $rules, ?callable $requestTransformer = null, ?callable $responseTransformer = null)
    {
        $this->rules = $rules;

        $this->requestTransformer = $requestTransformer
            ? $requestTransformer(...)
            : null;

        $this->responseTransformer = $responseTransformer
            ? $responseTransformer(...)
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
     * @param callable|null $requestTransformer
     * @param callable|null $responseTransformer
     * @return bool
     */
    public function tryCreateTransformers(RequestInterface $request, ?callable &$requestTransformer = null, ?callable &$responseTransformer = null): bool
    {
        $context = InterceptorContext::create();

        try {
            foreach ($this->rules as $rule) {
                if ($rule->tryMatch($request, $context)) {
                    continue;
                }

                return false;
            }

            $requestTransformer = function (RequestInterface $request, array $options) use ($context) {
                $transformer = $this->requestTransformer ?? fn(RequestInterface $request) => $request;
                return $transformer($request, $context, $options);
            };

            $responseTransformer = function (ResponseInterface $response) use ($context) {
                $transformer = $this->responseTransformer ?? fn(ResponseInterface $response) => $response;
                return $transformer($response, $context);
            };

            return true;
        }

        catch (Throwable $exception) {
            throw new RuntimeException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}