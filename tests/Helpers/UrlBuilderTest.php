<?php

declare(strict_types=1);

namespace WhereOnWebsite\Tests\Helpers;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WhereOnWebsite\Helpers\UrlBuilder;

class UrlBuilderTest extends TestCase
{
    /** @var UrlBuilder|PHPUnit_Framework_MockObject_MockObject */
    private $urlBuilderTraitMock;

    private const URL1 = '../../../some-page.html';
    private const URL2 = 'https://www.example.com/some-page.html';
    private const URL3 = 'http://example.com/';
    private const URL4 = 'https://example.com';
    private const URL5 = 'http://example.com/some-page/index.html';
    private const URL6 = 'articles/nature/climate-change';
    private const URL7 = '//example.com/news';
    private const URL8 = 'ws://example.com';
    private const URL9 = 'HTTps://www.example.com/';
    private const URL10 = '/store/ssds';
    private const URL11 = 'sub.www.example.com/online/';
    private const URL12 = 'http://www.example.com:8080/online/';
    private const URL13 = 'http://www.example.com/online/deals/november/index.html';

    protected function setUp(): void
    {
        $this->urlBuilderTraitMock = $this->getMockForTrait(UrlBuilder::class);
    }

    protected function tearDown(): void
    {
        $this->urlBuilderTraitMock = null;
    }

    public function testCountBackStepsReturnsCorrectValue()
    {
        $this->assertEquals(3, $this->urlBuilderTraitMock->countBackSteps(self::URL1));
        $this->assertEquals(0, $this->urlBuilderTraitMock->countBackSteps(self::URL2));
        $this->assertEquals(0, $this->urlBuilderTraitMock->countBackSteps(self::URL3));
        $this->assertEquals(0, $this->urlBuilderTraitMock->countBackSteps(self::URL4));
        $this->assertEquals(0, $this->urlBuilderTraitMock->countBackSteps(self::URL5));
        $this->assertEquals(0, $this->urlBuilderTraitMock->countBackSteps(self::URL6));
        $this->assertEquals(0, $this->urlBuilderTraitMock->countBackSteps(self::URL7));
        $this->assertEquals(0, $this->urlBuilderTraitMock->countBackSteps(self::URL8));
        $this->assertEquals(0, $this->urlBuilderTraitMock->countBackSteps(self::URL9));
        $this->assertEquals(0, $this->urlBuilderTraitMock->countBackSteps(self::URL10));
        $this->assertEquals(0, $this->urlBuilderTraitMock->countBackSteps(self::URL11));
        $this->assertEquals(0, $this->urlBuilderTraitMock->countBackSteps(self::URL12));
    }

    public function testGetPathReturnsCorrectValue()
    {
        $this->assertEquals('../../../some-page.html', $this->urlBuilderTraitMock->getPath(self::URL1));
        $this->assertEquals('/some-page.html', $this->urlBuilderTraitMock->getPath(self::URL2));
        $this->assertEquals('/', $this->urlBuilderTraitMock->getPath(self::URL3));
        $this->assertEquals('', $this->urlBuilderTraitMock->getPath(self::URL4));
        $this->assertEquals('/some-page/index.html', $this->urlBuilderTraitMock->getPath(self::URL5));
        $this->assertEquals('articles/nature/climate-change', $this->urlBuilderTraitMock->getPath(self::URL6));
        $this->assertEquals('/news', $this->urlBuilderTraitMock->getPath(self::URL7));
        $this->assertEquals('', $this->urlBuilderTraitMock->getPath(self::URL8));
        $this->assertEquals('/', $this->urlBuilderTraitMock->getPath(self::URL9));
        $this->assertEquals('/store/ssds', $this->urlBuilderTraitMock->getPath(self::URL10));
        $this->assertEquals('sub.www.example.com/online/', $this->urlBuilderTraitMock->getPath(self::URL11));
        $this->assertEquals('/online/', $this->urlBuilderTraitMock->getPath(self::URL12));
    }

    public function testGetHostReturnsCorrectValue()
    {
        $this->assertEquals(null, $this->urlBuilderTraitMock->getHost(self::URL1));
        $this->assertEquals('www.example.com', $this->urlBuilderTraitMock->getHost(self::URL2));
        $this->assertEquals('example.com', $this->urlBuilderTraitMock->getHost(self::URL3));
        $this->assertEquals('example.com', $this->urlBuilderTraitMock->getHost(self::URL4));
        $this->assertEquals('example.com', $this->urlBuilderTraitMock->getHost(self::URL5));
        $this->assertEquals(null, $this->urlBuilderTraitMock->getHost(self::URL6));
        $this->assertEquals('example.com', $this->urlBuilderTraitMock->getHost(self::URL7));
        $this->assertEquals('example.com', $this->urlBuilderTraitMock->getHost(self::URL8));
        $this->assertEquals('www.example.com', $this->urlBuilderTraitMock->getHost(self::URL9));
        $this->assertEquals(null, $this->urlBuilderTraitMock->getHost(self::URL10));
        $this->assertEquals(null, $this->urlBuilderTraitMock->getHost(self::URL11));
        $this->assertEquals('www.example.com', $this->urlBuilderTraitMock->getHost(self::URL12));
    }

    public function testGetBaseUrlThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid url. Must contain scheme.');
        $this->urlBuilderTraitMock->getBaseUrl(self::URL1);
    }

    public function testGetBaseUrlReturnsCorrectValue()
    {
        $this->assertEquals('https://www.example.com', $this->urlBuilderTraitMock->getBaseUrl(self::URL2));
        $this->assertEquals('http://example.com', $this->urlBuilderTraitMock->getBaseUrl(self::URL3));
        $this->assertEquals('https://example.com', $this->urlBuilderTraitMock->getBaseUrl(self::URL4));
        $this->assertEquals('http://example.com', $this->urlBuilderTraitMock->getBaseUrl(self::URL5));
        $this->assertEquals('ws://example.com', $this->urlBuilderTraitMock->getBaseUrl(self::URL8));
        $this->assertEquals('https://www.example.com', $this->urlBuilderTraitMock->getBaseUrl(self::URL9));
        $this->assertEquals('http://www.example.com:8080', $this->urlBuilderTraitMock->getBaseUrl(self::URL12));
    }

    public function testBuildDownstreamUrlReturnsCorrectValue()
    {
        $this->assertEquals('http://example.com/some-page/articles/nature/climate-change', 
            $this->urlBuilderTraitMock->buildDownstreamUrl(self::URL6, self::URL5));
        $this->assertEquals('http://example.com/articles/nature/climate-change', 
            $this->urlBuilderTraitMock->buildDownstreamUrl(self::URL6, self::URL3));
        $this->assertEquals('http://www.example.com:8080/online/articles/nature/climate-change', 
            $this->urlBuilderTraitMock->buildDownstreamUrl(self::URL6, self::URL12));
        $this->assertEquals('ws://example.com/articles/nature/climate-change', 
            $this->urlBuilderTraitMock->buildDownstreamUrl(self::URL6, self::URL8));
    }

    public function testBuildUpstreamUrlReturnsCorrectValue()
    {
        $this->assertEquals('http://www.example.com/some-page.html', 
            $this->urlBuilderTraitMock->buildUpstreamUrl(self::URL1, self::URL13));
        $this->assertEquals('http://example.com/store/ssds', 
            $this->urlBuilderTraitMock->buildUpstreamUrl('..'.self::URL10, self::URL5));
        $this->assertEquals('http://www.example.com:8080/articles/nature/climate-change', 
            $this->urlBuilderTraitMock->buildUpstreamUrl('../'.self::URL6, self::URL12));
        $this->assertEquals('http://sub.www.example.com/articles/nature/climate-change', 
            $this->urlBuilderTraitMock->buildUpstreamUrl('../'.self::URL6, 'http://'.self::URL11));   
    }

    public function testBuildAbsoluteUrlReturnsCorrectValue()
    {
        $this->assertEquals('http://www.example.com/some-page.html', 
            $this->urlBuilderTraitMock->buildAbsoluteUrl(self::URL1, self::URL13));
        $this->assertEquals('http://sub.www.example.com/articles/nature/climate-change', 
            $this->urlBuilderTraitMock->buildAbsoluteUrl('../'.self::URL6, 'http://'.self::URL11));
        $this->assertEquals('http://example.com/articles/nature/climate-change', 
            $this->urlBuilderTraitMock->buildAbsoluteUrl(self::URL6, self::URL3));
        $this->assertFalse($this->urlBuilderTraitMock->buildAbsoluteUrl('//google.com', self::URL3));
        $this->assertFalse($this->urlBuilderTraitMock->buildAbsoluteUrl('http://google.com', self::URL5));
        $this->assertEquals('http://www.example.com/store/ssds', 
            $this->urlBuilderTraitMock->buildAbsoluteUrl(self::URL10, self::URL13));
        $this->assertFalse($this->urlBuilderTraitMock->buildAbsoluteUrl(self::URL7, self::URL13));
        $this->assertEquals('//www.example.com/news', 
            $this->urlBuilderTraitMock->buildAbsoluteUrl('//www.example.com/news', self::URL13));
        $this->assertEquals('HTTps://www.example.com/', 
            $this->urlBuilderTraitMock->buildAbsoluteUrl(self::URL9, self::URL2));
    }

}