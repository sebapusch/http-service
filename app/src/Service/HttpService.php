<?php

namespace App\Service;

use App\Service\HttpService\Exception\InvalidMethodException;
use App\Service\HttpService\Exception\InvalidUrlFormatException;
use App\Service\HttpService\Request;
use App\Service\HttpService\Response;

class HttpService implements HttpServiceInterface
{
    /**
     * @param Request $request
     * @return Response
     * @throws HttpService\Exception\InvalidStatusCodeException
     */
    public function sendRequest(Request $request): Response
    {
        $handle = fopen($request->getUrl(), 'r', false, $request->getContext());
        $meta = stream_get_meta_data($handle);
        $content = stream_get_contents($handle);

        return Response::build($meta, $content);
    }
}