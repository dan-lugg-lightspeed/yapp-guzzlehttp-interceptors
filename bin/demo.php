<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Yapp\GuzzleHttp\Interceptors\MiddlewareFactoryBuilder;

require_once sprintf("%s/../vendor/autoload.php", __DIR__);

///
/// 
/// 
/// 
/// 

function main(): void
{
    $middlewareFactoryBuilder = MiddlewareFactoryBuilder::buildWith(function (MiddlewareFactoryBuilder $builder) {
        $builder->get("/*", [
            "incoming" => function (RequestInterface $request, array $options): RequestInterface {
                echo "Incoming-A\n";
                return $request->withHeader("Incoming-A", true);
            },
            "outgoing" => function (ResponseInterface $response, array $options): ResponseInterface {
                echo "Outgoing-A\n";
                return $response->withHeader("Outgoing-A", true);
            }
        ]);

        $builder->get("/foo/*", [
            "incoming" => function (RequestInterface $request, array $options): RequestInterface {
                echo "Incoming-B\n";
                return $request->withHeader("Incoming-B", true);
            },
            "outgoing" => function (ResponseInterface $response, array $options): ResponseInterface {
                echo "Outgoing-B\n";
                return $response->withHeader("Outgoing-B", true);
            }
        ]);

        $builder->get("/foo/bar/*", [
            "incoming" => function (RequestInterface $request, array $options): RequestInterface {
                echo "Incoming-C\n";
                return $request->withHeader("Incoming-C", true);
            },
            "outgoing" => function (ResponseInterface $response, array $options): ResponseInterface {
                echo "Outgoing-C\n";
                return $response->withHeader("Outgoing-C", true);
            }
        ]);
    });

    $request = new Request("GET", "/foo/bar");

    $handlerStack = HandlerStack::create();
    $handlerStack->setHandler(new CurlHandler());

    foreach ($middlewareFactoryBuilder->getMiddlewareCallables($request) as $middlewareCallable) {
        $handlerStack->push($middlewareCallable);
    };

    $client = new Client(["handler" => $handlerStack, "debug" => true]);
    $response = $client->get("https://example.com/");

    var_dump($response->getStatusCode());

    foreach ($response->getHeaders() as $name => $values) {
        echo vsprintf("%-15s\t%s\n", [
            $name,
            implode(", ", $values),
        ]);
    }

    var_dump($response->getBody());
}

main();
