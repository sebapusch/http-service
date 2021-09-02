<?php

namespace App\Service\HttpService;

use App\Service\HttpService\Exception\InvalidStatusCodeException;
use App\Service\HttpServiceInterface;
use Exception;

class Response
{
    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @var array
     */
    private mixed $content;

    /**
     * @var \DateTimeImmutable
     */
    private \DateTimeImmutable $datetime;

    /**
     * @var array
     */
    private array $headers;

    /**
     * @param int $statusCode
     * @param array $headers
     * @param array $content
     * @throws InvalidStatusCodeException
     */
    protected function __construct(
        int $statusCode,
        \DateTimeImmutable $datetime,
        array $headers = [],
        mixed $content = []
    )
    {
        $this->setStatusCode($statusCode);
        $this->datetime = $datetime;
        $this->headers = $headers;
        $this->content = $content;
    }

    /**
     * @param int $statusCode
     * @throws InvalidStatusCodeException
     */
    protected function setStatusCode(int $statusCode): void
    {
        if(false === in_array($statusCode, HttpServiceInterface::statusCodes)) {
            throw new InvalidStatusCodeException('Invalid Status Code');
        }

        $this->statusCode = $statusCode;
    }

    /**
     * @param array $content
     */
    protected function setContent(array $content)
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @param array $meta
     * @throws InvalidStatusCodeException
     * @throws Exception
     */
    public static function build(array $meta, string $content): Response
    {
        $wrapperData =  str_split(
            $meta['wrapper_data'][0],
            strpos($meta['wrapper_data'][0], ' ')
        );
        $statusCode = intval($wrapperData[1]);
        $dateTime = new \DateTimeImmutable(
            str_replace('Date: ', '', $meta['wrapper_data'][1])
        );

        return new Response(
            $statusCode,
            $dateTime,
            $meta,
            $content
        );
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDatetime(): \DateTimeImmutable
    {
        return $this->datetime;
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
}