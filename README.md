# tsehay-bank-api-client

[![Latest Stable Version](https://img.shields.io/github/v/release/brokeyourbike/tsehay-bank-api-client-php)](https://github.com/brokeyourbike/tsehay-bank-api-client-php/releases)
[![Total Downloads](https://poser.pugx.org/brokeyourbike/tsehay-bank-api-client/downloads)](https://packagist.org/packages/brokeyourbike/tsehay-bank-api-client)
[![Maintainability](https://api.codeclimate.com/v1/badges/4a40e4d17f270cd65f32/maintainability)](https://codeclimate.com/github/brokeyourbike/tsehay-bank-api-client-php/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/4a40e4d17f270cd65f32/test_coverage)](https://codeclimate.com/github/brokeyourbike/tsehay-bank-api-client-php/test_coverage)

Tsehay Bank API Client for PHP

## Installation

```bash
composer require brokeyourbike/tsehay-bank-api-client
```

## Usage

```php
use BrokeYourBike\TsehayBank\Client;
use BrokeYourBike\TsehayBank\Interfaces\ConfigInterface;

assert($config instanceof ConfigInterface);
assert($httpClient instanceof \GuzzleHttp\ClientInterface);

$apiClient = new Client($config, $httpClient);
```

## Authors
- [Ivan Stasiuk](https://github.com/brokeyourbike) | [Twitter](https://twitter.com/brokeyourbike) | [LinkedIn](https://www.linkedin.com/in/brokeyourbike) | [stasi.uk](https://stasi.uk)

## License
[Mozilla Public License v2.0](https://github.com/brokeyourbike/tsehay-bank-api-client-php/blob/main/LICENSE)