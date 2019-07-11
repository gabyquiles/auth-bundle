<?php

namespace GabyQuiles\Auth\Loaders;


use CoderCat\JWKToPEM\JWKConverter;

class JwkKeyLoader
{
    /**
     * @var JWKConverter
     */
    private $converter;

    public function __construct()
    {
        $converter = new JWKConverter();
        $this->converter = $converter;
    }

    public function loadKey($kid)
    {
//TODO: Refactor this
        $rawJwk = '{"keys":[{"alg":"RS256","e":"AQAB","kid":"oNCHU4tKVtanHJZE1D/f1oUY5z6cPJ9WbsKRKzb8SZg=","kty":"RSA","n":"t6MhnSOa_rG64ndms_rZcQtyrPEgEGp0KLuANEl-EFYNBmng91POvszUt7FcT2u-2Hvinw9AoPucgWJgI6rjHI8-kf81t-HW0MwnSXWvv-8Ym--EyJjKnIzjxxbHN1SpTyl93ylSfpyImbgpHD2wjrXlzw6BMAUurZjS7tX6H0ELHKeA8tHr338DaFrEVcJDSC7Kic4jx2TFUlaJTf7gDq8tPK0lpgFr7ZXwP6jj00ckXGi4_UETtT-DtBKvSIImJ22wWOUlTdTvvF8YqmeN5Sf0rQFgoo033YHlTcv6x9WlsMc8Yj5ONU31ALulbMP27oDiuwJ8BaaeL4v3noYuzw","use":"sig"},{"alg":"RS256","e":"AQAB","kid":"h2zfWyffu9XG04R/yCjtzfu7tJDIctW46mbFfoiT7Lk=","kty":"RSA","n":"rgic_jps95kQVKDcmC5l30SYwvlMwufVl6ur2PyPRAHCnD8vmkntJu1iIWgp0oubwg5RI61XkN9m0PvV42I8kzqvb1s26tKjs57TvueZ1e7qoksF_pHWUkUyDz3BuCrGVRYYRp0DrAmmLFEveIY6QUMNwXX5QWBYhJiBJemucfmZcaWqdlosOAwmjE8s2dVV9S8fOb2ZdF0FuJCARENdaKiryT721GI7sYNEwm4FLl_c_5mChMw-ms6ZurjOCa0HOE-GBCMPHSFGIHy7zjWFogERLVp6xvj2fC5fW9Cm72xizSjxIjYzEGa6tasGqabpL3xWrox4lY4fy6bN1SCaJQ","use":"sig"}]}';
        $jwks = json_decode($rawJwk, true);
        $keys = [];
        foreach ($jwks['keys'] as $key) {
            $keys[$key['kid']] = $key;
        }
        return $this->converter->toPEM($keys[$kid]);
    }
}