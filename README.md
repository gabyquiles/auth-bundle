# JWT Auth Bundle
This bundle decode and verifies a AWS JWT to allow users authenticated with AWS Cognito to access your services.

## Installation

### Configuration
Add config/gaby_quiles_auth_jws.yaml
```
gaby_quiles_auth_jws:
  token_ttl: 3601
  clock_skew: 1
  pool_id: '%pool_id%'
  region: '%aws_region%'
```