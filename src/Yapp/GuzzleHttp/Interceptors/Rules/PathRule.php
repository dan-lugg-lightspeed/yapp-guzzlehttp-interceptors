<?php

namespace Yapp\GuzzleHttp\Interceptors\Rules;

use Exception;
use Psr\Http\Message\RequestInterface;
use Yapp\GuzzleHttp\Interceptors\InterceptorContextInterface;

class PathRule implements RuleInterface
{
    /**
     * @var bool
     */
    private bool $isWildcard;

    /**
     * @var array|PathPartMatcherInterface[]
     */
    private array $pathPartMatchers;

    /**
     * @param bool $isWildcard
     * @param array|PathPartMatcherInterface[] $pathPartMatchers
     */
    public function __construct(bool $isWildcard, array $pathPartMatchers)
    {
        $this->isWildcard = $isWildcard;
        $this->pathPartMatchers = $pathPartMatchers;
    }

    /**
     * @return bool
     */
    public function isWildcard(): bool
    {
        return $this->isWildcard;
    }

    /**
     * @return array|PathPartMatcherInterface[]
     */
    public function getPathPartMatchers(): array
    {
        return $this->pathPartMatchers;
    }

    /**
     * @param RequestInterface $request
     * @param InterceptorContextInterface $interceptorContext
     * @return bool
     * @throws PathRuleMatchingException
     */
    public function tryMatch(RequestInterface $request, InterceptorContextInterface &$interceptorContext): bool
    {
        $path = $request->getUri()->getPath();
        $pathParts = preg_split("@/@", $path, -1, PREG_SPLIT_NO_EMPTY);

        if (count($pathParts) < count($this->pathPartMatchers)) {
            return false;
        }

        if (count($pathParts) > count($this->pathPartMatchers) && !$this->isWildcard) {
            return false;
        }

        $params = [];

        foreach ($this->pathPartMatchers as $pathPartMatcher) {
            try {
                $pathPart = array_shift($pathParts);

                if ($pathPartMatcher->tryMatch($pathPart, $params)) {
                    continue;
                }
            }

            catch (Exception $exception) {
                $message = vsprintf("Failed to match on the path part where path part is `%s` for request of `%s %s`", [
                    $pathPart,
                    $request->getMethod(),
                    $request->getUri()->getPath(),
                ]);

                throw new PathRuleMatchingException($message, 0, $exception);
            }

            return false;
        }

        $interceptorContext = $interceptorContext->withParams($params);

        if (count($pathParts) > 0) {
            $interceptorContext = $interceptorContext->withPathParts($pathParts);
        }

        return true;
    }
}