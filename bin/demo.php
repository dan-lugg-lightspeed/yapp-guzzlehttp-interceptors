<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Yapp\GuzzleHttp\Interceptors\InterceptorContextInterface;
use Yapp\GuzzleHttp\Interceptors\InterceptorMiddleware;
use Yapp\GuzzleHttp\Interceptors\InterceptorMiddlewareBuilder;
use Yapp\GuzzleHttp\Interceptors\InterceptorMiddlewareBuildingException;

require_once sprintf("%s/../vendor/autoload.php", __DIR__);


class Interceptors
{
    /**
     * @throws InterceptorMiddlewareBuildingException
     */
    public static function createMiddleware(): InterceptorMiddleware
    {
        return InterceptorMiddlewareBuilder::buildWith(function (InterceptorMiddlewareBuilder $middlewareBuilder) {
            $middlewareBuilder->any("/*")
                ->withRequestTransformer([self::class, "exampleRequestTransformer1"])
                ->withResponseTransformer([self::class, "exampleResponseTransformer1"]);

            $middlewareBuilder->any("/admin/{module}/*")
                ->withRequestTransformer([self::class, "exampleRequestTransformer2"])
                ->withResponseTransformer([self::class, "exampleResponseTransformer2"]);
        });
    }

    public static function exampleRequestTransformer1(RequestInterface $request, InterceptorContextInterface $context): RequestInterface
    {
        var_dump([__METHOD__ => $context]);
        return $request;
    }

    public static function exampleResponseTransformer1(ResponseInterface $response, InterceptorContextInterface $context): ResponseInterface
    {
        var_dump([__METHOD__ => $context]);
        return $response;
    }

    public static function exampleRequestTransformer2(RequestInterface $request, InterceptorContextInterface $context): RequestInterface
    {
        var_dump([__METHOD__ => $context]);
        return $request;
    }

    public static function exampleResponseTransformer2(ResponseInterface $response, InterceptorContextInterface $context): ResponseInterface
    {
        var_dump([__METHOD__ => $context]);
        return $response;
    }
}

/**
 * @throws GuzzleException
 * @throws InterceptorMiddlewareBuildingException
 */
function main(): void
{
    $v2Middleware = Interceptors::createMiddleware();

    $handlerStack = HandlerStack::create();
    $handlerStack->setHandler(new CurlHandler());
    $handlerStack->push($v2Middleware);
    $client = new Client([
        "handler" => $handlerStack,
        "verify" => false,
        // "debug" => true,
    ]);

    $response = $client->get("https://localhost:3000/admin/invoicing/movies/5");

    var_dump($response->getBody()->getContents());
}

main();
