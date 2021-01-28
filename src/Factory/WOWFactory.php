<?php

namespace WhereOnWebsite\Factory;

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;
use WhereOnWebsite\WOW;
use WhereOnWebsite\Parser\Parser;
use Nyholm\Psr7\Factory\Psr17Factory;

class WOWFactory
{

    /**
     * Creates and returns WOW instance.
     * 
     * @return WOW
     */
    public static function create()
    {
        $client = new Client();
        $requestFactory = new Psr17Factory();
        $parser = new Parser(new Dom());

        return new WOW($client, $requestFactory, $parser);
    }
}