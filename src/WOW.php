<?php

namespace WhereOnWebsite;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use WhereOnWebsite\Contracts\ParserInterface;

class WOW
{
    /**
     * @var Psr\Http\Client\ClientInterface
     */
    private $client;

    /**
     * @var Psr\Http\Message\RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var WhereOnWebsite\Contracts\ParserInterface
     */
    private $parser;

    /**
     * List of urls yet to be scanned.
     * @var array
     */
    private $urlsLeftToScan = [];

    /**
     * List of urls scanned or attempted to scan.
     * @var array
     */
    private $urlsScanned = [];

    /**
     * List of urls that contain searched text.
     * @var array
     */
    private $urlsWithMatch = [];

    /**
     * Searched text.
     * @var string
     */
    private $text;

    /**
     * Max number of web pages to attempt to scan.
     * @var int
     */
    private $limit;

    /**
     * Currently scanned url.
     * @var string
     */
    private $currentUrl;

    public function __construct(ClientInterface $client,
                                RequestFactoryInterface $requestFactory, 
                                ParserInterface $parser)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->parser = $parser;
    }

    /**
     * Gets client instance
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Sets client instance
     * @return WOW $this
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Checks if the maximum number of web pages to scan hasn't been reached yet.
     * 
     * @return bool
     */
    private function isWithinLimit()
    {
        return count($this->urlsScanned) < $this->limit;
    }

    /**
     * Checks if any there are urls left to scan.
     * 
     * @return bool
     */
    private function hasNext()
    {
        return !empty($this->urlsLeftToScan);
    }

    /**
     * Returns url and moves it from left to scan to scanned urls list.
     * 
     * @return string $url
     */
    private function next()
    {
        $url = $this->urlsLeftToScan[0]; 
        unset($this->urlsLeftToScan[0]);

        $this->urlsScanned[] = $url;
        $this->urlsLeftToScan = array_values($this->urlsLeftToScan);

        return $url;
    }

    /**
     * Searches for text on internal web pages of a website.
     * 
     * @param string $text - text to search
     * @param string $url - url to start searching from
     * @param int $limit - max number of web pages to attempt to scan.
     * 
     * @return WOW $this
     */
    public function searchText($text, $url, $limit = 100)
    {
        $this->urlsLeftToScan[] = $url;
        $this->text = $text;
        $this->limit = $limit;

        while ( $this->hasNext() && $this->isWithinLimit() ) {

            $this->currentUrl = $this->next();
            $this->processCurrentUrl();
        }

        return $this;
    }

    /**
     * Scans currently processed url for new urls and searched text matches
     * @return void
     */
    private function processCurrentUrl()
    {
        $request = $this->requestFactory->createRequest('GET', $this->currentUrl);
        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() === 200) {

            $html = $response->getBody();
            $internalAbsoluteUrls = $this->parser->getInternalAbsoluteUrls($html, $this->currentUrl);

            foreach ($internalAbsoluteUrls as $url) {

                    if ( !$this->wasUrlAlreadyDiscovered($url) ) {
                        $this->urlsLeftToScan[] = $url;
                    }

            }

            if ( $this->checkHasMatch($html) ) {
                $this->urlsWithMatch[] = $this->currentUrl;
            }

        }
    }

    /**
     * Check is html contains searched text
     * @param string $html
     * @return bool
     */
    private function checkHasMatch($html)
    {
        return $this->parser->hasMatch($html, $this->text);
    }

    /**
     * Checks if url was already scanned or queuing to be scanned
     * @param string $url
     * @param bool
     */
    private function wasUrlAlreadyDiscovered($url)
    {
       return in_array($url, $this->urlsLeftToScan) || in_array($url, $this->urlsScanned);
    }

    /**
     * Returns list of urls of web pages containing searched text.
     * @return array $this->urlsWithMatch
     */
    public function getMatched()
    {
        return $this->urlsWithMatch;
    }

    public function getScanned()
    {
        return $this->urlsScanned;
    }
}