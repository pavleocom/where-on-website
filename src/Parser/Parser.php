<?php

namespace WhereOnWebsite\Parser;

use PHPHtmlParser\Dom;
use InvalidArgumentException;
use WhereOnWebsite\Contracts\ParserInterface;
use WhereOnWebsite\Helpers\UrlBuilder;

class Parser implements ParserInterface
{
    use UrlBuilder;

    private $domParser;

    public function __construct($domParser = null)
    {
        if ($domParser === null) {
            $this->domParser = new Dom();
        } else {
            $this->domParser = $domParser;
        }
    }

    public function getDomParser()
    {
        return $this->domParser;
    }

    public function setDomParser($domParser)
    {
        $this->domParser = $domParser;
        return $this;
    }

    /**
     * Returns a list of absolute urls extracted from the html.
     * @param string $html
     * @param string $url ($url of the web page containing the html)
     * @return array $internalAbsoluteUrls (no duplicates)
     */
    public function getInternalAbsoluteUrls(string $html, string $url): array
    {
        $internalAbsoluteUrls = [];
        $this->domParser->loadStr($html);
        $anchors = $this->domParser->find('a');

        foreach ($anchors as $anchor) {

            if ($anchor->hasAttribute('href')) {

                $hrefValue = $anchor->getAttribute('href');
                $absoluteUrl = $this->buildAbsoluteUrl($hrefValue, $url);

                if ($absoluteUrl !== false) {
                    $internalAbsoluteUrls[] = $absoluteUrl;
                }

            }
        }

        return array_unique($internalAbsoluteUrls);
    }

    public function hasMatch(string $html, string $text): bool
    {
        if (strpos($html, $text) !== false) {
            return true;
        }

        return false;
    }
}