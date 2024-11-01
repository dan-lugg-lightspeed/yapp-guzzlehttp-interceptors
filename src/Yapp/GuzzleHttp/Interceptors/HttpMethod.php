<?php

namespace Yapp\GuzzleHttp\Interceptors;

class HttpMethod
{
    public const string GET = "GET";
    public const string POST = "POST";
    public const string PUT = "PUT";
    public const string DELETE = "DELETE";
    public const string PATCH = "PATCH";
    public const string OPTIONS = "OPTIONS";
    public const string HEAD = "HEAD";
    public const string TRACE = "TRACE";
    public const string CONNECT = "CONNECT";

    private function __construct()
    {
    }
}
