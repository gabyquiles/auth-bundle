<?php

namespace GabyQuiles\Auth\Loaders;


use Symfony\Component\HttpClient\HttpClient;

class JwkFetcher
{
    /**
     * @var string
     */
    private $url;

    public function __construct($poolId, $region = 'us-east-1')
    {
        $this->url = 'https://cognito-idp.' . $region . '.amazonaws.com/' . $poolId . '/.well-known/jwks.json';
    }

    public function getJwk()
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $this->url);

        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            $content = $response->getContent();
            return json_decode($content, true);
        }
        throw new \RuntimeException();
    }
}