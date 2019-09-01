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

    public function __construct(LoggerInterface $logger, $poolId, $region = 'us-east-1')
    {
        $this->url = 'https://cognito-idp.' . $region . '.amazonaws.com/' . $poolId . '/.well-known/jwks.json';
        $this->logger = $logger;
    }

    public
    function getJwk()
    {
        $this->logger->debug("Requesting JWK");
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $this->url);

        $this->logger->debug("Received status code: " . $response->getStatusCode());
        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            $this->logger->debug("Got JWK");
            $content = $response->getContent();
            return json_decode($content, true);
        }

        $this->logger->debug("Error getting JWK");
        throw new \RuntimeException();
    }
}