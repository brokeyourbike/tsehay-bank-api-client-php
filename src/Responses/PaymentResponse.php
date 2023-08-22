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
class PaymentResponse extends JsonResponse
{
    #[MapFrom('header.status')]
    public ?string $status;

    /** @var \BrokeYourBike\TsehayBank\Responses\ErrorDetail[] */
    #[CastWith(ArrayCaster::class, ErrorDetail::class)]
    #[MapFrom('error.errorDetails')]
    public ?array $errors;
}

class ErrorDetail extends DataTransferObject
{
    public string $code;
    public string $message;
    public string $fieldName;
}

