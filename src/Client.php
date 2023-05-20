<?php

// Copyright (C) 2023 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\TsehayBank;

use GuzzleHttp\ClientInterface;
use BrokeYourBike\TsehayBank\Models\TransactionResponse;
use BrokeYourBike\TsehayBank\Interfaces\TransactionInterface;
use BrokeYourBike\TsehayBank\Interfaces\ConfigInterface;
use BrokeYourBike\ResolveUri\ResolveUriTrait;
use BrokeYourBike\HttpEnums\HttpMethodEnum;
use BrokeYourBike\HttpClient\HttpClientTrait;
use BrokeYourBike\HttpClient\HttpClientInterface;
use BrokeYourBike\HasSourceModel\SourceModelInterface;
use BrokeYourBike\HasSourceModel\HasSourceModelTrait;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class Client implements HttpClientInterface
{
    use HttpClientTrait;
    use ResolveUriTrait;
    use HasSourceModelTrait;

    private ConfigInterface $config;

    public function __construct(ConfigInterface $config, ClientInterface $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function payment(TransactionInterface $transaction): TransactionResponse
    {
        $options = [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->config->getToken()}",
            ],
            \GuzzleHttp\RequestOptions::JSON => [
                'body' => [
                    'creditCurrencyId' => $transaction->getCurrencyCode(),
                    'creditAccountId' => $transaction->getBankAccount(),
                    'creditAmount' => $transaction->getAmount(),
                    'paymentDetails' => [
                        ['paymentDetail' => $transaction->getReference()],
                    ],
                ],
            ],
        ];

        if ($transaction instanceof SourceModelInterface){
            $options[\BrokeYourBike\HasSourceModel\Enums\RequestOptions::SOURCE_MODEL] = $transaction;
        }

        $uri = (string) $this->resolveUriFor($this->config->getUrl(), "tsehayBank/payments/{$this->config->getFrom()}");
        $response = $this->httpClient->request(HttpMethodEnum::POST->value, $uri, $options);

        return new TransactionResponse($response);
    }
}
