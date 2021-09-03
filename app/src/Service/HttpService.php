<?php

namespace App\Service;

use App\Service\HttpService\Request;
use App\Service\HttpService\Response;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class HttpService implements HttpServiceInterface
{
    private LoggerInterface $logger;
    private MailerInterface $mailer;
    private array $emails;
    private bool $loggingEnabled;

    public function __construct(LoggerInterface $httpLogger, MailerInterface $mailer)
    {
        $this->logger = $httpLogger;
        $this->mailer = $mailer;
        $this->loggingEnabled = false;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws HttpService\Exception\InvalidStatusCodeException
     * @throws Exception
     */
    public function sendRequest(Request $request): Response
    {
        $handle = fopen($request->getUrl(), 'r', false, $request->getContext());
        $meta = stream_get_meta_data($handle);

        $content = stream_get_contents($handle);
        $response = Response::build($meta, $content);

        if($this->loggingEnabled) {
            $this->createLog($request, $response);
        }

        $statusCodeGroup = substr($response->getStatusCode(), 0, 1);
        if($statusCodeGroup === '4' || $statusCodeGroup === '5') {
            $this->sendEmail($request, $response);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    private function createLog(Request $request, Response $response): void
    {
        $this->logger->info(sprintf('Request: %s Response: %s',
            json_encode($request->jsonSerialize()),
            json_encode($response->jsonSerialize())
        ));
    }

    private function sendEmail(Request $request, Response $response): void
    {
        if(empty($this->emails)) {
            return;
        }

        $email = (new Email())
            ->from($this->emails['sender'])
            ->to($this->emails['receiver'])
            ->subject('Failed with status ' . $response->getStatusCode())
            ->text(sprintf('Request: %s Response: %s',
                json_encode($request->jsonSerialize()),
                json_encode($response->jsonSerialize())
            ));

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->warning('Ops');
        }
    }

    /**
     * @param array $emails
     * @return bool
     */
    public function setUpEmails(array $emails): bool
    {
        if(
            !isset($emails['receiver']) || !isset($emails['sender']) ||
            !filter_var($emails['receiver'],FILTER_VALIDATE_EMAIL) ||
            !filter_var($emails['sender'],FILTER_VALIDATE_EMAIL)
        ) {
            return false;
        }

        $this->emails = [
            'sender' => $emails['sender'],
            'receiver' => $emails['receiver']
        ];

        return true;
    }

    public function enableLogging(): void
    {
        $this->loggingEnabled = true;
    }
}