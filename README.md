# tsehay-bank-api-client

[![Latest Stable Version](https://img.shields.io/github/v/release/brokeyourbike/tsehay-bank-api-client-php)](https://github.com/brokeyourbike/tsehay-bank-api-client-php/releases)
[![Total Downloads](https://poser.pugx.org/brokeyourbike/tsehay-bank-api-client/downloads)](https://packagist.org/packages/brokeyourbike/tsehay-bank-api-client)

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