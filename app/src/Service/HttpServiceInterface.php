<?php

namespace App\Service;

use App\Service\HttpService\Request;
use App\Service\HttpService\Response;

interface HttpServiceInterface
{
    public const HTTP_GET = 'GET';
    public const HTTP_POST = 'POST';
    public const HTTP_PUT = 'PUT';
    public const HTTP_PATCH = 'PATCH';

    public const HTTP_OK = 'ok';
    public const HTTP_NOT_FOUND = 'not_found';
    /** @todo add */

    public const statusCodes = [
        HttpServiceInterface::HTTP_OK => 200,
        HttpServiceInterface::HTTP_NOT_FOUND => 404
    ];

    /**
     * @param Request $request
     * @return Response
     */
    public function sendRequest(Request $request): Response;
}