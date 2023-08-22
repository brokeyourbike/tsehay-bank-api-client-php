<?php

// Copyright (C) 2023 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\TsehayBank\Tests;

use Psr\Http\Message\ResponseInterface;
use BrokeYourBike\TsehayBank\Responses\PaymentResponse;
use BrokeYourBike\TsehayBank\Interfaces\TransactionInterface;
use BrokeYourBike\TsehayBank\Interfaces\ConfigInterface;
use BrokeYourBike\TsehayBank\Client;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class PaymentTest extends TestCase
{
    /** @test */
    public function it_can_prepare_request(): void
    {
        $transaction = $this->getMockBuilder(TransactionInterface::class)->getMock();
        $transaction->method('getReference')->willReturn('ref-123');
        $transaction->method('getAccountNumber')->willReturn('12345');
        $transaction->method('getCurrencyCode')->willReturn('USD');
        $transaction->method('getAmount')->willReturn(50.00);

        /** @var TransactionInterface $transaction */
        $this->assertInstanceOf(TransactionInterface::class, $transaction);

        $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $mockedConfig->method('getUrl')->willReturn('https://api.example/');
        $mockedConfig->method('getFrom')->willReturn('EXAMPLE');
        $mockedConfig->method('getToken')->willReturn('super-secure-token');

        $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockedResponse->method('getStatusCode')->willReturn(200);
        $mockedResponse->method('getBody')
            ->willReturn('{
                "linkedActivities": [
                    {
                        "header": {
                            "transactionStatus": "Live",
                            "audit": {
                                "versionNumber": "1"
                            },
                            "id": "QJDB",
                            "status": "success"
                        },
                        "body": {
                            "arrangementId": "92K3",
                            "activityId": "ACCOUNTS-CREDIT-ARRANGEMENT",
                            "productId": "USD.SAVING",
                            "currencyId": "USD",
                            "effectiveDate": "2023-06-05"
                        }
                    }
                ],
                "header": {
                    "transactionStatus": "Live",
                    "audit": {
                        "T24_time": 5013,
                        "responseParse_time": 12,
                        "requestParse_time": 12,
                        "versionNumber": "1"
                    },
                    "id": "XXGN",
                    "status": "success"
                },
                "body": {
                    "transactionType": "ACGT",
                    "orderingCust": "CURRENCY",
                    "debitCurrencyId": "USD",
                    "processingDate": "2023-06-05",
                    "creditCurrencyId": "USD",
                    "debitAccountId": "10001",
                    "debitAmount": 100,
                    "creditAccountId": "12345",
                    "paymentDetails": [
                        {
                            "paymentDetail": "ref-123"
                        }
                    ]
                }
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'POST',
            'https://api.example/tsehayBank/payments/EXAMPLE',
            [
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer super-secure-token",
                ],
                \GuzzleHttp\RequestOptions::JSON => [
                    'body' => [
                        'debitCurrencyId' => 'USD',
                        'debitAmount' => 50.00,
                        'creditAccountId' => '12345',
                        'paymentDetails' => [
                            ['paymentDetail' => 'ref-123'],
                        ],
                    ],
                ],
            ],
        ])->once()->andReturn($mockedResponse);

        /**
         * @var ConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($mockedConfig, $mockedClient);

        $requestResult = $api->payment($transaction);
        $this->assertInstanceOf(PaymentResponse::class, $requestResult);
        $this->assertEquals('success', $requestResult->status);
    }
}