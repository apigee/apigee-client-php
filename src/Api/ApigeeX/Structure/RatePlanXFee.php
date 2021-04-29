<?php

/*
 * Copyright 2021 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Api\ApigeeX\Structure;

use Apigee\Edge\Api\ApigeeX\Entity\Property\CurrencyCodePropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Entity\Property\NanosPropertyAwareTrait;
use Apigee\Edge\Api\ApigeeX\Entity\Property\UnitsPropertyAwareTrait;
use Apigee\Edge\Structure\BaseObject;

/**
 * Class RatePlanXFee.
 *
 * TODO: Add documenation link
 */
final class RatePlanXFee extends BaseObject
{
    use CurrencyCodePropertyAwareTrait;
    use UnitsPropertyAwareTrait;
    use NanosPropertyAwareTrait;
}
