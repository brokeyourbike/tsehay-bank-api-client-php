<?php

// Copyright (C) 2023 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\TsehayBank\Tests;

use Psr\Http\Message\ResponseInterface;
use BrokeYourBike\TsehayBank\Responses\AccountResponse;
use BrokeYourBike\TsehayBank\Interfaces\TransactionInterface;
use BrokeYourBike\TsehayBank\Interfaces\ConfigInterface;
use BrokeYourBike\TsehayBank\Client;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class AccountTest extends TestCase
{
    /** @test */
    public function it_can_prepare_request(): void
    {
        $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $mockedConfig->method('getUrl')->willReturn('https://api.example/');
        $mockedConfig->method('getToken')->willReturn('super-secure-token');

        $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockedResponse->method('getStatusCode')->willReturn(200);
        $mockedResponse->method('getBody')
            ->willReturn('{
                "header": {
                    "audit": {
                        "T24_time": 267,
                        "responseParse_time": 7,
                        "requestParse_time": 15
                    },
                    "page_start": 1,
                    "total_size": 1,
                    "page_size": 99,
                    "status": "success"
                },
                "body": [
                    {
                        "accountId": "account123",
                        "accountName": "JOHN DOE"
                    }
                ]
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->withArgs([
            'GET',
            'https://api.example/tsehayBank/account/account123/name',
            [
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer super-secure-token",
                ],
            ],
        ])->once()->andReturn($mockedResponse);

        /**
         * @var ConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($mockedConfig, $mockedClient);

        $requestResult = $api->accountName('account123');
        $this->assertInstanceOf(AccountResponse::class, $requestResult);
        $this->assertEquals('success', $requestResult->status);
        $this->assertCount(1, $requestResult->accounts);
        $this->assertEquals('JOHN DOE', $requestResult->accounts[0]->name);
        $this->assertEquals('account123', $requestResult->accounts[0]->number);
    }

    /** @test */
    public function it_can_handle_error(): void
    {
        $mockedConfig = $this->getMockBuilder(ConfigInterface::class)->getMock();
        $mockedConfig->method('getUrl')->willReturn('https://api.example/');
        $mockedConfig->method('getToken')->willReturn('super-secure-token');

        $mockedResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $mockedResponse->method('getStatusCode')->willReturn(200);
        $mockedResponse->method('getBody')
            ->willReturn('{
                "header": {
                    "audit": {
                        "T24_time": 264,
                        "responseParse_time": 8,
                        "requestParse_time": 15
                    },
                    "status": "failed"
                },
                "error": {
                    "code": "TGVCP-007",
                    "message": "No records were found that matched the selection criteria",
                    "type": "BUSINESS"
                }
            }');

        /** @var \Mockery\MockInterface $mockedClient */
        $mockedClient = \Mockery::mock(\GuzzleHttp\Client::class);
        $mockedClient->shouldReceive('request')->once()->andReturn($mockedResponse);

        /**
         * @var ConfigInterface $mockedConfig
         * @var \GuzzleHttp\Client $mockedClient
         * */
        $api = new Client($mockedConfig, $mockedClient);

        $requestResult = $api->accountName('account123');
        $this->assertInstanceOf(AccountResponse::class, $requestResult);
        $this->assertEquals('failed', $requestResult->status);
        $this->assertEquals('TGVCP-007', $requestResult->errorCodename);
        $this->assertEquals('No records were found that matched the selection criteria', $requestResult->errorMessage);
    }
}
