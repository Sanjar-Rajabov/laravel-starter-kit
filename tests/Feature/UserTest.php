<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_create()
    {
        $method = 'get';
        $url = 'user';

        $response = $this->call('get', 'user');
        dd($method, $url, json_decode($response->getContent()));
    }
}
