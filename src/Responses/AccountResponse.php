<?php

// Copyright (C) 2023 Ivan Stasiuk <ivan@stasi.uk>.
//
// This Source Code Form is subject to the terms of the Mozilla Public
// License, v. 2.0. If a copy of the MPL was not distributed with this file,
// You can obtain one at https://mozilla.org/MPL/2.0/.

namespace BrokeYourBike\TsehayBank\Responses;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Attributes\CastWith;
use BrokeYourBike\DataTransferObject\JsonResponse;

/**
 * @author Ivan Stasiuk <ivan@stasi.uk>
 */
class AccountResponse extends JsonResponse
{
    #[MapFrom('header.status')]
    public ?string $status;

    #[MapFrom('error.code')]
    public ?string $errorCodename;

    #[MapFrom('error.message')]
    public ?string $errorMessage;

    /** @var \BrokeYourBike\TsehayBank\Responses\Account[] */
    #[CastWith(ArrayCaster::class, Account::class)]
    #[MapFrom('body')]
    public ?array $accounts;
}

class Account extends DataTransferObject
{
    #[MapFrom('accountId')]
    public string $number;

    #[MapFrom('accountName')]
    public string $name;
}