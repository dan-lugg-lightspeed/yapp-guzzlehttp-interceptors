<?php

namespace Yapp\GuzzleHttp\Interceptors;

interface InterceptorContextInterface
{
    /**
     * @return array
     */
    public function getParams(): array;

    /**
     * @return array|string[]
     */
    public function getPathParts(): array;

    /**
     * @param array $params
     * @return $this
     */
    public function withParams(array $params): static;

    /**
     * @param array $pathParts
     * @return $this
     */
    public function withPathParts(array $pathParts): static;
}