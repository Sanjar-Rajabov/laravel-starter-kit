<?php

namespace Tests;

class PostmanTestCase extends TestCase
{
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }
}

