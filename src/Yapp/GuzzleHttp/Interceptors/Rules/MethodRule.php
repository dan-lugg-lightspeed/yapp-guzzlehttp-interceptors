<?php

namespace Yapp\GuzzleHttp\Interceptors\Rules;

use Exception;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Yapp\GuzzleHttp\Interceptors\HttpMethod;
use Yapp\GuzzleHttp\Interceptors\InterceptorContextInterface;

class MethodRule implements RuleInterface
{
    /**
     * @var string
     */
    private string $method;

    /**
     * @param string $method
     * @throws Exception
     */
    public function __construct(string $method)
    {
        switch ($method = strtoupper($method)) {
            case HttpMethod::CONNECT:
            case HttpMethod::DELETE:
            case HttpMethod::GET:
            case HttpMethod::HEAD:
            case HttpMethod::OPTIONS:
            case HttpMethod::PATCH:
            case HttpMethod::POST:
            case HttpMethod::PUT:
            {
                $this->method = $method;
                break;
            }

            default:
            {
                $message = vsprintf("Invalid HTTP method of `%s`", [
                    $method,
                ]);

                throw new InvalidArgumentException($message);
            }
        }
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param RequestInterface $request
     * @param InterceptorContextInterface $interceptorContext
     * @return bool
     */
    function tryMatch(RequestInterface $request, InterceptorContextInterface &$interceptorContext): bool
    {
        $method = $request->getMethod();
        return strcasecmp($method, $this->method) === 0;
    }
}