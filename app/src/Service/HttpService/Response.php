<?php

namespace App\Service\HttpService;

use App\Service\HttpService\Exception\InvalidStatusCodeException;
use App\Service\HttpServiceInterface;
use DateTimeImmutable;
use Exception;

class Response implements \JsonSerializable
{
    /**
     * @var array
     */
    private array $status;

    /**
     * var string
     */
    private string $httpVersion;

    /**
     * @var array
     */
    private mixed $content;

    /**
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $datetime;

    /**
     * @var array
     */
    private array $headers;

    /**
     * @param array $status
     * @param string $httpVersion
     * @param DateTimeImmutable $datetime
     * @param array $headers
     * @param array $content
     * @throws InvalidStatusCodeException
     */
    protected function __construct(
        array $status,
        string $httpVersion,
        DateTimeImmutable $datetime,
        array $headers = [],
        mixed $content = []
    )
    {
        $this->setStatus($status);
        $this->httpVersion = $httpVersion;
        $this->datetime = $datetime;
        $this->headers = $headers;
        $this->content = $content;
    }

    /**
     * @param array $content
     */
    protected function setContent(array $content)
    {
        $this->content = $content;
    }

    /**
     * @param array $status
     * @throws InvalidStatusCodeException
     */
    protected function setStatus(array $status): void
    {
        if(empty($status['code']) || empty($status['message'])) {
            throw new InvalidStatusCodeException('Invalid Status');
        }

        $this->status = $status;
    }

    /**
     * @param string $httpVersion
     */
    protected function setHttpVersion(string $httpVersion): void
    {
        $this->httpVersion = $httpVersion;
    }

    /**
     * @param DateTimeImmutable $datetime
     */
    protected function setDatetime(DateTimeImmutable $datetime): void
    {
        $this->datetime = $datetime;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->status['code'];
    }

    /**
     * @return array
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getHttpVersion(): string
    {
        return $this->httpVersion;
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
     * @param string $content
     * @return Response
     * @throws InvalidStatusCodeException
     * @throws Exception
     */
    public static function build(array $meta, string $content): Response
    {
        $httpInfo = explode(' ', trim($meta['wrapper_data'][0]), 3);

        $httpVersion = $httpInfo[0];
        $status = [
            'code' => intval($httpInfo[1]),
            'message' => $httpInfo[2]
        ];

        unset($meta['wrapper_data'][0]);

        $headers = self::parseHeaders($meta['wrapper_data']);
        $dateTime = (new DateTimeImmutable($headers['Date']))
            ->setTimezone(new \DateTimeZone('UTC'));

        return new Response(
            $status,
            $httpVersion,
            $dateTime,
            $headers,
            mb_convert_encoding($content, 'UTF-8')
        );
    }

    /**
     * @param array $headers
     * @return array
     */
    private static function parseHeaders(array $headers): array
    {
        $parsed = [];
        foreach ($headers as $header) {

            $splitHeader = explode(':', $header, 2);
            $parsed[$splitHeader[0]] = substr($splitHeader[1], 1);
        }

        return $parsed;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDatetime(): DateTimeImmutable
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

    public function jsonSerialize()
    {
        return [
            'status' => $this->status,
            'httpVersion' => $this->httpVersion,
            'dateTime' => $this->datetime->format('Y-m-d H:i:s'),
            'content' => $this->content,
            'headers' => $this->headers,
        ];
    }

}