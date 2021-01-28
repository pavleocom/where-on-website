<?php

namespace WhereOnWebsite\Contracts;

interface ParserInterface
{
    public function getInternalAbsoluteUrls(string $html, string $url): array;
    public function hasMatch(string $html, string $text): bool;
}