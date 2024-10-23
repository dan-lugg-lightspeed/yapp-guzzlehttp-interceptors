<?php

namespace Yapp\GuzzleHttp\Interceptors\Rules;

interface PathPartMatcherParserInterface
{
    /**
     * @param string $pathPartPattern
     * @return PathPartMatcherInterface
     */
    public function parse(string $pathPartPattern): PathPartMatcherInterface;
}