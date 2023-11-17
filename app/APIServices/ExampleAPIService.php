<?php

namespace App\APIServices;

use App\APIServices\Core\APIService;
use Exception;

class ExampleAPIService extends APIService
{
    public function __construct()
    {
        $this->setBaseUrl(env('EXAMPLE_URL'));
    }

    /**
     * @throws Exception
     */
    public static function getInfo(int $id): object
    {
        $service = new self;

        return $service->get('example', [
            'id' => $id
        ])->object()->data;
    }
}
