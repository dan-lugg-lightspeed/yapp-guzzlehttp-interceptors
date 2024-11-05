<?php

namespace Yapp\GuzzleHttp\Interceptors;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Yapp\GuzzleHttp\Interceptors\Rules\MethodRule;
use Yapp\GuzzleHttp\Interceptors\Rules\PathRuleParser;

class InterceptorFactory implements InterceptorFactoryInterface
{
    /**
     * @var PathRuleParser
     */
    private PathRuleParser $pathRuleParser;

    /**
     * @param PathRuleParser $pathRuleParser
     */
    public function __construct(PathRuleParser $pathRuleParser)
    {
        $this->pathRuleParser = $pathRuleParser;
    }

    /**
     * @param string|null $method
     * @param string $pathPattern
     * @param callable(RequestInterface):RequestInterface|null $requestTransformer
     * @param callable(ResponseInterface):ResponseInterface|null $responseTransformer
     * @return InterceptorInterface
     * @throws InterceptorCreationException
     */
    public function createInterceptor(?string $method, string $pathPattern, ?callable $requestTransformer = null, ?callable $responseTransformer = null): InterceptorInterface
    {
        $rules = [];

        try {
            if ($method !== null) {
                $rules[] = new MethodRule($method);
            }

            $rules[] = $this->pathRuleParser->parse($pathPattern);
            return new Interceptor($rules, $requestTransformer, $responseTransformer);
        }

        catch (Throwable $exception) {
            $message = vsprintf("Failed to create interceptor where method is `%s` and path pattern is `%s`", [
                $method ?? "*",
                $pathPattern,
            ]);

            throw new InterceptorCreationException($message, 0, $exception);
        }
    }
}