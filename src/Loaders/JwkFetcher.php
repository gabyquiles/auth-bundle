<?php

namespace GabyQuiles\Auth\Loaders;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;

class JwkFetcher
{
    /**
     * @var string
     */
    private $url;

    /** @var LoggerInterface */
    private $logger;

    public function __construct($poolId, $region = 'us-east-1', LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->url = 'https://cognito-idp.' . $region . '.amazonaws.com/' . $poolId . '/.well-known/jwks.json';
    }

    public function getJwk()
    {
        $this->debug("Requesting JWK");
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $this->url);

        $this->debug("Received status code: " . $response->getStatusCode());
        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            $this->logger->debug("Got JWK");
            $content = $response->getContent();
            return json_decode($content, true);
        }

        $this->debug("Error getting JWK");
        throw new \RuntimeException();
    }

    private function debug($message) {
        if($this->logger) {
            $this->logger->debug($message);
        }
    }
}