<?php

namespace Yapp\GuzzleHttp\Interceptors\Rules;

interface PathRuleParserInterface
{
    /**
     * @param string $pathPattern
     * @return PathRule
     */
    public function parse(string $pathPattern): PathRule;
}