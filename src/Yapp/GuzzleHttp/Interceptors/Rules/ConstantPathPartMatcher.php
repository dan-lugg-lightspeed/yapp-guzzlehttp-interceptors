<?php

namespace Yapp\GuzzleHttp\Interceptors\Rules;

use InvalidArgumentException;

class ConstantPathPartMatcher implements PathPartMatcherInterface
{
    /**
     * @var string
     */
    private string $constant;

    /**
     * @param string $constant
     * @throws InvalidArgumentException
     */
    public function __construct(string $constant)
    {
        if (empty($constant)) {
            throw new InvalidArgumentException("Constant must not be empty");
        }

        $this->constant = $constant;
    }

    /**
     * @return string
     */
    public function getConstant(): string
    {
        return $this->constant;
    }

    /**
     * @param string $pathPart
     * @param array $params
     * @return bool
     */
    public function tryMatch(string $pathPart, array &$params = []): bool
    {
        return strcasecmp($pathPart, $this->constant) === 0;
    }
}