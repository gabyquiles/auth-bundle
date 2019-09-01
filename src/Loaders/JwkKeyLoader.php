<?php

namespace GabyQuiles\Auth\Loaders;


use CoderCat\JWKToPEM\JWKConverter;
use Symfony\Contracts\Cache\CacheInterface;

class JwkKeyLoader
{
    /**
     * @var JWKConverter
     */
    private $converter;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var JwkFetcher
     */
    private $jwkFetcher;


    public function __construct(CacheInterface $cache, JwkFetcher $jwkFetcher)
    {
        $converter = new JWKConverter();
        $this->converter = $converter;
        $this->cache = $cache;
        $this->jwkFetcher = $jwkFetcher;
    }

    public function loadKey($kid)
    {
        $jwks = $this->cache->get('gaby_quiles_auth_jws.jwk_keys', function ($item) {
            $item->expiresAfter(null);
            return $this->jwkFetcher->getJwk();
        });
        $keys = [];
        foreach ($jwks['keys'] as $key) {
            $keys[$key['kid']] = $key;
        }
        return $this->converter->toPEM($keys[$kid]);
    }
}