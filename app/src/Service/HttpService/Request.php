<?php

namespace App\Service\HttpService;

use App\Service\HttpService\Exception\InvalidMethodException;
use App\Service\HttpService\Exception\InvalidUrlFormatException;
use App\Service\HttpServiceInterface;

class Request implements \JsonSerializable
{
    private string $url;
    private string $method;
    private array $content;
    private array $queryParams;
    private array $headers;

    /**
     * @param string $url
     * @param string $method
     * @param array $content
     * @param array $queryParams
     * @param array $headers
     * @throws InvalidMethodException
     * @throws InvalidUrlFormatException
     */
    public function __construct(
        string $url,
        string $method,
        array $content = [],
        array $queryParams = [],
        array $headers = []
    )
    {
        if(false === $this->validateMethod($method)) {
            throw new InvalidMethodException('Invalid method provided');
        }
        if(false === $this->validateUrl($url)) {
            throw new InvalidUrlFormatException('Invalid URL provided');
        }

        $this->url = $url;
        $this->method = $method;
        $this->content = $content;
        $this->queryParams = $queryParams;
        $this->headers = $headers;
    }

    /**
     * @param string $method
     * @return bool
     */
    private function validateMethod(string $method): bool
    {
        if (
            $method === HttpServiceInterface::HTTP_GET ||
            $method === HttpServiceInterface::HTTP_POST ||
            $method === HttpServiceInterface::HTTP_PUT ||
            $method === HttpServiceInterface::HTTP_PATCH
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param string $url
     * @return bool
     */
    private function validateUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return true;
    }

    /**
     * @return resource
     */
    public function getContext()
    {
        $header = '';
        foreach($this->headers as $value) {
            $header = sprintf("%s\r\n%s:%s", $header, $value['key'], $value['value']);
        }

        $options = ['http' => [
            'method' => $this->method,
            'header' => $header,
            'content' => $this->content,
            'ignore_errors' => true
        ]];

        return stream_context_create($options);
    }

    /**
     * @return string
     */
    public function getUrl(bool $withQueryParams = true): string
    {
        return (!$withQueryParams || empty($this->queryParams))
            ? $this->url
            : $this->buildUrlWithQueryParams();
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @return string
     */
    private function buildUrlWithQueryParams(): string
    {
        $url = $this->url . '?';
        $counter = 0;

        foreach ($this->queryParams as $value) {
            $url = ($counter === 0)
                ? sprintf('%s%s=%s', $url, $value['key'], $value['value'])
                : sprintf('%s&%s=%s', $url, $value['key'], $value['value']);

            $counter ++;
        }

        return $url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @param array $content
     */
    public function setContent(array $content): void
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }


    public function jsonSerialize()
    {
        return [
            'url' => $this->url,
            'method' => $this->method,
            'headers' => $this->headers,
            'queryParams' => $this->queryParams,
            'content' => $this->content,
        ];
    }
}