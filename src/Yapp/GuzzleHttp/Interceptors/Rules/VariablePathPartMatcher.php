<?php

namespace Yapp\GuzzleHttp\Interceptors\Rules;

use InvalidArgumentException;

class VariablePathPartMatcher implements PathPartMatcherInterface
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string|null
     */
    private ?string $pathPartPattern;

    /**
     * @param string $name
     * @param string|null $pathPartPattern
     * @throws InvalidArgumentException
     */
    public function __construct(string $name, ?string $pathPartPattern)
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Name cannot be empty');
        }

        $this->name = $name;
        $this->pathPartPattern = $pathPartPattern;
    }

    /**
     * @param string $pathPart
     * @param array $params
     * @return bool
     */
    public function tryMatch(string $pathPart, array &$params = []): bool
    {
        if ($this->pathPartPattern === null) {
            $params[$this->name] = $pathPart;
            return true;
        }

        if (preg_match($this->pathPartPattern, $pathPart)) {
            $params[$this->name] = $pathPart;
            return true;
        }

        return false;
    }
}