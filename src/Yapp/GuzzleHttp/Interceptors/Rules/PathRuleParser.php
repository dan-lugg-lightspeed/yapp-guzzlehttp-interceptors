<?php

namespace Yapp\GuzzleHttp\Interceptors\Rules;

use Exception;

class PathRuleParser implements PathRuleParserInterface
{
    private const string REGEX_SPLIT = "@/@";
    private const string PATH_PART_WILDCARD = "*";

    /**
     * @var PathPartMatcherParserInterface
     */
    private PathPartMatcherParserInterface $pathPartMatcherParser;

    /**
     * @param PathPartMatcherParserInterface $pathPartMatcherParser
     */
    public function __construct(PathPartMatcherParserInterface $pathPartMatcherParser)
    {
        $this->pathPartMatcherParser = $pathPartMatcherParser;
    }

    /**
     * @param string $pathPattern
     * @return PathRule
     * @throws PathRuleParsingException
     */
    public function parse(string $pathPattern): PathRule
    {
        $pathPartPatterns = preg_split(self::REGEX_SPLIT, $pathPattern, -1, PREG_SPLIT_NO_EMPTY);
        $pathPartMatchers = [];

        while (count($pathPartPatterns) > 0) {
            $pathPartPattern = array_shift($pathPartPatterns);

            if ($pathPartPattern === self::PATH_PART_WILDCARD) {
                if (count($pathPartPatterns) > 0) {
                    $message = vsprintf("Wildcards only permitted at end of path pattern, path pattern is `%s`", [
                        $pathPattern,
                    ]);

                    throw new PathRuleParsingException($message);
                }

                return new PathRule(true, $pathPartMatchers);
            }

            try {
                $pathPartMatchers[] = $this->pathPartMatcherParser->parse($pathPartPattern);
            }

            catch (Exception $exception) {
                $message = vsprintf("Failed to parse path part pattern of `%s`", [
                    $pathPartPattern,
                ]);

                throw new PathRuleParsingException($message, 0, $exception);
            }
        }

        return new PathRule(false, $pathPartMatchers);
    }
}