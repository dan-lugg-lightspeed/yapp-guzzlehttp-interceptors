# 🔌 yapp-guzzlehttp-interceptors

## What Is This?

Yet Another PHP Package for Guzzle middleware interceptors.

This package allows the dynamic inclusion of Guzzle middleware functions, based on inspection of the request before it
is sent. In short, it acts as a sort of "routing" interceptor framework, that bundles middleware functions together
based on assertions such as URI path matching, the HTTP method, and so on.

## Example

First a `MiddlewareFactory` is created with defined interceptor rules and request/response transformation functions that
will be bundled up into Guzzle middleware. In this case:

- `GET /*`
    - Translates to any `GET` request.
- `GET /foo/*`
    - Translates to any `GET` request under `/foo`

```php
$middlewareFactory = MiddlewareFactoryBuilder::buildWith(function (MiddlewareFactoryBuilder $builder) {
    $builder->get("/*", [
        "incoming" => function (RequestInterface $request, array $options): RequestInterface {
            echo "Incoming-A\n";
            return $request->withHeader("Incoming-A", true);
        },
        "outgoing" => function (ResponseInterface $response, array $options): ResponseInterface {
            echo "Outgoing-A\n";
            return $response->withHeader("Outgoing-A", true);
        },
    ]);

    $builder->get("/foo/*", [
        "incoming" => function (RequestInterface $request, array $options): RequestInterface {
            echo "Incoming-B\n";
            return $request->withHeader("Incoming-B", true);
        },
        "outgoing" => function (ResponseInterface $response, array $options): ResponseInterface {
            echo "Outgoing-B\n";
            return $response->withHeader("Outgoing-B", true);
        },
    ]);
});
```

Now, we simply get all the applicable middleware functions based on a request:

```php
$request = new Request("GET", "/foo/bar");
$middlewares = $middleWareFactory->getMiddlewareCallables($request);
```

And then attach them to a Guzzle `HandlerStack`:

```php
$handlerStack = HandlerStack::create();
$handlerStack->setHandler(new CurlHandler());

foreach ($middlewares as $middleware) {
    $handlerStack->push($middleware);
}
```

And when the request is dispatched through a Guzzle client, the corresponding request/response headers are attached, and we should see:

```text
Incoming-A
Incoming-B
Outgoing-B
Outgoing-A
```