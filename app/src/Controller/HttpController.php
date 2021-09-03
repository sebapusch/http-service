<?php

namespace App\Controller;

use App\Service\HttpService\Exception\InvalidMethodException;
use App\Service\HttpService\Exception\InvalidUrlFormatException;
use App\Service\HttpServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\HttpService\Request as ClientRequest;

class HttpController extends AbstractController
{
    private HttpServiceInterface $httpService;

    public function __construct(HttpServiceInterface $httpService, string $errorStatusEmail)
    {
        $this->httpService = $httpService;
        $this->httpService->enableLogging();
        $this->httpService->setUpEmails([
            'sender' => $errorStatusEmail,
            'receiver' => $errorStatusEmail
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createRequest(Request $request): JsonResponse
    {
        $url = $request->get('url');
        $method =  $request->get('method');
        $content = $request->get('content', []);
        $queryParams = $request->get('queryParams', []);
        $headers = $request->get('headers', []);

        try {
            if(empty($url)) {
                throw new InvalidArgumentException('The url is required');
            }
            if(empty($method)) {
                throw new InvalidArgumentException('The method is required');
            }

            $request = new ClientRequest(
                $url,
                $method,
                $content,
                $queryParams,
                $headers,
            );

            $response = $this->httpService->sendRequest($request);

            return new JsonResponse($response->jsonSerialize());
        } catch (InvalidUrlFormatException | InvalidMethodException | InvalidArgumentException $exception) {

            return new JsonResponse(['error' => $exception->getMessage()], 400);
        } catch (\Exception $exception) {

            echo $exception->getMessage();

            return new JsonResponse(['error' => $exception->getFile()], 500);
        }
    }
}