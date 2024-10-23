<?php

namespace Yapp\GuzzleHttp\Interceptors\Rules;

class PathPartMatcherParser implements PathPartMatcherParserInterface
{
    public const string REGEX_NAME_AND_PATTERN = "@^{(?<name>[a-zA-Z_][a-zA-Z0-9_]*):(?<pattern>[^}]+)}$@";

    public const string REGEX_NAME_AND_ANY = "@^{(?<name>[a-zA-Z_][a-zA-Z0-9_]*)}$@";

    /**
     * @var array
     */
    private array $namedPatterns = [];

    /**
     * @param string $pathPartPattern
     * @return PathPartMatcherInterface
     */
    public function parse(string $pathPartPattern): PathPartMatcherInterface
    {
        $matches = [];

        if (preg_match(self::REGEX_NAME_AND_PATTERN, $pathPartPattern, $matches)) {
            $name = $matches["name"];
            $pattern = vsprintf("@^%s$@", [
                $this->namedPatterns[$matches["pattern"]] ?? $matches["pattern"],
            ]);

            return new VariablePathPartMatcher($name, $pattern);
        }

        if (preg_match(self::REGEX_NAME_AND_ANY, $pathPartPattern, $matches)) {
            $name = $matches["name"];
            return new VariablePathPartMatcher($name, null);
        }

        return new ConstantPathPartMatcher($pathPartPattern);
    }
}