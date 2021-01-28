<?php

namespace WhereOnWebsite\Helpers;

use InvalidArgumentException;

trait UrlBuilder
{

        /**
     * Returns absolute URL of internal web page or false if external URL;
     * @param string $hrefValue
     * @param string $url (of the webpage containing anchor element with the above href value)
     * @return string|bool
     */
    public function buildAbsoluteUrl(string $hrefValue, string $url) {

        if (preg_match('/^\/[^\/]/', $hrefValue) === 1) { 
            // starts with / but not // - internal root 
            return $this->getBaseUrl($url) . $hrefValue;

        } else if (preg_match('/^[^\/]/', $hrefValue) === 1 
                    && strpos($hrefValue, ':') === false) { 
            // does not start with / and does not contain : - internal relative downstream
            return $this->buildDownstreamUrl($hrefValue, $url);

        } else if (preg_match('/^\.\./', $hrefValue) === 1) { 
            // starts with .. - internal relative upstream
            return $this->buildUpstreamUrl($hrefValue, $url);

        } else if (strpos($hrefValue, $this->getBaseUrl($url)) === 0) { 
            // internal absolute
            return $hrefValue;

        } else if (preg_match('/^\/\//', $hrefValue)
                    && $this->getHost($hrefValue) === $this->getHost($url)) {
            // internal absolute without scheme
            return  $hrefValue;

        } else {

            return false; // external link or invalid

        }
    }

    /**
     * Extracts base url from absolute url. Must be absolute url scheme://host
     */
    public function getBaseUrl(string $url)
    {
        $parts = parse_url($url);

        if (!isset($parts['scheme'])) {
            throw new InvalidArgumentException('Invalid url. Must contain scheme.');
        }

        $baseUrl = strtolower($parts['scheme']) . '://' . $parts['host'];

        if (isset($parts['port'])) {
            $baseUrl .= ':' . $parts['port'];
        }

        return $baseUrl;
    }

    /**
     * Returns host or null if could not extract host
     * @param string $url
     * @return string|null
     */
    public function getHost(string $url)
    {
        $parts = parse_url($url);

        if (isset($parts['host'])) {
            return $parts['host'];
        }

        return null;
    }

    /**
     * Returns path
     * @param string $url
     * @return string (path)
     */
    public function getPath(string $url)
    {
        $parts = parse_url($url);

        if (isset($parts['path'])) {
            return $parts['path'];
        }

        return '';
    }

    /**
     * Turns relative downstream url to absolute url.
     * @param string $relativeUrl
     * @param string $url (absolute url of the page where $relativeUrl is referenced)
     * @return string (absolute url)
     */
    public function buildDownstreamUrl(string $relativeUrl, string $url)
    {
        $baseUrl = $this->getBaseUrl($url);

        if (trim($url, '/') === $baseUrl) {

            return $baseUrl . '/' . $relativeUrl;

        } else if ($url[strlen($url)-1] === '/') {

            return $url . $relativeUrl;

        } else {
            $excessLenth = strlen(strrchr($url, '/'));
            $absoluteUrl = substr($url, 0, strlen($url) - $excessLenth) . '/' . $relativeUrl;

            if (substr($absoluteUrl, 0, 4) !== 'http') {
                $absoluteUrl = preg_replace('/^(.*)(:\/\/)(.*)$/i', "http$2$3", $absoluteUrl, 1);
            }

            return $absoluteUrl;
        }
    }

    /**
     * Turns relative upstream url to absolute url.
     * @param string $relativeUrl
     * @param string $url (absolute url of the page where $relativeUrl is referenced)
     * @return string|false (absolute url)
     */
    public function buildUpstreamUrl(string $relativeUrl, string $url)
    {
        $backSteps = $this->countBackSteps($relativeUrl);
        $path = trim($this->getPath($url), '/');
        $pathParts = explode('/', $path);
        $pathPartsCount = count($pathParts);

        if ($backSteps <= $pathPartsCount) {
            $remainingParts = array_slice($pathParts, 0, $pathPartsCount - $backSteps);

            preg_match('/^[../]*(?<remaining>.*)$/', $relativeUrl, $matches);

            if (isset($matches['remaining'])) {
                return $this->getBaseUrl($url) . '/' . implode('/', $remainingParts) . '/' . $matches['remaining'];
            }
            
        }

        return false;
    }

    public function countBackSteps($url)
    {
        $parts = explode('/', $url);
        $count = 0;

        foreach ($parts as $part) {
            if ($part == '..') {
                $count++;
            }
        }

        return $count;
    }
}