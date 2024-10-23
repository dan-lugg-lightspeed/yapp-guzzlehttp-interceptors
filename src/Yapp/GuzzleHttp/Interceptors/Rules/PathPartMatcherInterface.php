<?php

namespace Yapp\GuzzleHttp\Interceptors\Rules;

interface PathPartMatcherInterface
{
    /**
     * @param string $pathPart
     * @param array $params
     * @return bool
     */
    public function tryMatch(string $pathPart, array &$params = []): bool;
}