<?php

namespace App\Tests\Controller;

use App\Controller\HttpController;
use App\Service\HttpServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use App\Service\HttpService\Response as ServiceResponse;
use App\Service\HttpService\Request as ServiceRequest;

final class HttpControllerTest extends TestCase
{
    private HttpController $controller;
    private HttpServiceInterface $httpService;

    protected function setUp(): void
    {
        $this->httpService = $this->createMock(HttpServiceInterface::class);
        $this->controller = new HttpController(
            $this->httpService,
            'test@email.com'
        );
    }

    public function testCreateRequestEmptyUrlBadRequest()
    {
        $request = $this->createJsonRequestMock(
          [
              'method' => 'POST'
          ]
        );

        $response = $this->controller->createRequest($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testCreateRequestEmptyMethodBadRequest()
    {
        $request = $this->createJsonRequestMock(
            [
                'url' => 'https://www.some-url.com'
            ]
        );

        $response = $this->controller->createRequest($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testCreateRequestInvalidUrlBadRequest()
    {
        $request = $this->createJsonRequestMock(
            [
                'url' => 'https/www.incorrectly-formatted-url.com',
                'method' => 'POST'
            ]
        );

        $response = $this->controller->createRequest($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testCreateRequestInvalidMethodBadRequest()
    {
        $request = $this->createJsonRequestMock(
            [
                'url' => 'https://www.test.com',
                'method' => 'PASTA'
            ]
        );

        $response = $this->controller->createRequest($request);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testCreateRequestUnexpectedErrorInternalServerError()
    {
        $request = $this->createJsonRequestMock(
            [
                'url' => 'https://www.test.com',
                'method' => 'POST'
            ]
        );

        $this->httpService
            ->method('sendRequest')
            ->willThrowException(new \Exception('Something went wrong'));

        $response = $this->controller->createRequest($request);

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testCreateRequestOK()
    {
        $serviceResponse = $this->createServiceResponseMock();
        $request = $this->createJsonRequestMock(
            [
                'url' => 'https://www.test.com',
                'method' => 'POST'
            ]
        );

        $this->httpService->method('sendRequest')
            ->with(new ServiceRequest('https://www.test.com', 'POST', [], [], []))
            ->willReturn($serviceResponse);

        $response = $this->controller->createRequest($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @param array $jsonBody
     * @return Request
     */
    private function createJsonRequestMock(array $jsonBody): Request
    {
        return new Request(
            request: $jsonBody
        );
    }

    private function createServiceResponseMock()
    {
        return ServiceResponse::build([
            'wrapper_data' => [
                'HTTP/1.1 200 OK',
                'Date: Fri, 03 Sep 2021 07:48:06 GMT'
            ]
        ], json_encode([
            'message' => 'Hello World'
        ]));
    }
}