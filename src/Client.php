<?php

// Copyright (C) 2023 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\TsehayBank;

use GuzzleHttp\ClientInterface;
use BrokeYourBike\TsehayBank\Responses\PaymentResponse;
use BrokeYourBike\TsehayBank\Responses\AccountResponse;
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

    public function payment(TransactionInterface $transaction): PaymentResponse
    {
        $options = [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->config->getToken()}",
            ],
            \GuzzleHttp\RequestOptions::JSON => [
                'body' => [
                    'debitCurrencyId' => $transaction->getCurrencyCode(),
                    'debitAmount' => $transaction->getAmount(),
                    'creditAccountId' => $transaction->getAccountNumber(),
                    'paymentDetails' => [
                        ['paymentDetail' => $transaction->getReference()],
                    ],
                ],
            ],
        ];

        if ($transaction instanceof SourceModelInterface){
            $options[\BrokeYourBike\HasSourceModel\Enums\RequestOptions::SOURCE_MODEL] = $transaction;
        }

        $uri = $this->prepareUri("tsehayBank/payments/{$this->config->getFrom()}");
        $response = $this->httpClient->request(HttpMethodEnum::POST->value, $uri, $options);

        return new PaymentResponse($response);
    }

    public function accountName(string $accountNumber): AccountResponse
    {
        $options = [
            \GuzzleHttp\RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$this->config->getToken()}",
            ],
        ];

        $uri = $this->prepareUri("tsehayBank/account/{$accountNumber}/name");
        $response = $this->httpClient->request(HttpMethodEnum::GET->value, $uri, $options);

        return new AccountResponse($response);
    }

    private function prepareUri(string $path): string
    {
        return (string) $this->resolveUriFor(rtrim($this->config->getUrl(), '/'), $path);
    }
}
