<?php

namespace App\Controller;

use App\Service\HttpService;
use App\Service\HttpServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    private HttpServiceInterface $client;

    public function __construct(HttpServiceInterface $client)
    {
        $this->client = $client;
    }

    public function test()
    {
        $request = new HttpService\Request(
            url: 'https://test-http-service.free.beeceptor.com/test',
            method: HttpServiceInterface::HTTP_GET,
            queryParams: ['key' => 'value', 'key2' => 'value2']
        );
        echo 'Request url: ' . $request->getUrl();
        echo "\n\n";
        $response = $this->client->sendRequest($request);
        echo 'Time: ' . $response->getDatetime()->format('Y-m-d H:i:s');

        die(0);
    }
}