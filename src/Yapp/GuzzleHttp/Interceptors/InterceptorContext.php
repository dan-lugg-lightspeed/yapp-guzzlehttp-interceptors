<?php

namespace Yapp\GuzzleHttp\Interceptors;

class InterceptorContext implements InterceptorContextInterface
{
    /**
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @var array
     */
    private array $params;

    /**
     * @var array|string[]
     */
    private array $pathParts;

    /**
     * @param array $params
     * @param array|string[] $pathParts
     */
    public function __construct(array $params = [], array $pathParts = [])
    {
        $this->params = $params;
        $this->pathParts = $pathParts;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return array|string[]
     */
    public function getPathParts(): array
    {
        return $this->pathParts;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function withParams(array $params): static
    {
        return (clone $this)->setParams($params);
    }

    /**
     * @param array $pathParts
     * @return $this
     */
    public function withPathParts(array $pathParts): static
    {
        return (clone $this)->setPathParts($pathParts);
    }

    /**
     * @param array $params
     * @return $this
     */
    private function setParams(array $params): static
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * @param array $pathParts
     * @return $this
     */
    private function setPathParts(array $pathParts): static
    {
        $this->pathParts = $pathParts;
        return $this;
    }
}