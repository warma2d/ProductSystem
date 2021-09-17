<?php

namespace ProductSystem\Core\Service;

class UriHandler {
    public static function parseId(string $uri): int
    {
        preg_match('%/(\d*)/%', $uri, $m);
        return $m[1]*1;
    }
}
