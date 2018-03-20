<?php

/**
 * Copyright 2018 Google LLC
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

namespace Apigee\Edge\Exception;

use Throwable;

/**
 * Class CpsNotEnabledException.
 *
 * For those cases if someone tries to add a CPS limit to an API call but the feature is not enabled on the
 * organization on Edge.
 *
 *
 * @see https://docs.apigee.com/api-services/content/api-reference-getting-started#cps
 */
class CpsNotEnabledException extends \RuntimeException
{
    /**
     * @var string
     */
    protected $organization;

    /**
     * CpsNotEnabledException constructor.
     *
     * @param string $organization
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $organization, $code = 0, Throwable $previous = null)
    {
        $this->organization = $organization;
    }

    public function __toString()
    {
        return sprintf('Core Persistence Services is not enabled on %s organization.', $this->organization);
    }
}
