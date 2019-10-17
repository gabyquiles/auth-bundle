# JWT Auth Bundle
This bundle decode and verifies a AWS JWT to allow users authenticated with AWS Cognito to access your services.

## Installation
`composer require gabyquiles/auth-bundle`

### Configuration
Add config/gaby_quiles_auth_jws.yaml
```
gaby_quiles_auth_jws:
  token_ttl: 3601
  clock_skew: 1
  pool_id: '%pool_id%'
  region: '%aws_region%'
```

### Mocking
When you are testing your application you want to isolate your application. In those cases you can use override the provider in `/config/services_test.yaml` for the TestProvider:
```
services:
  gaby_quiles_auth_jws.aws_jwt_provider:
    class: GabyQuiles\Auth\Providers\TestProvider
```

This provider will receive a base64 encoded json token like:  
`{"username": "admin","exp": 1570899818,"iat": 1570896218,"email": "user@example.com"}`