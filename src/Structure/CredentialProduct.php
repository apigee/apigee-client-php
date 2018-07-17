<?php

/*
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

namespace Apigee\Edge\Structure;

use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;

/**
 * Describes a item in the list of API products included in a credential.
 */
class CredentialProduct implements CredentialProductInterface
{
    use StatusPropertyAwareTrait;

    /** @var string Name of the API product entity. */
    protected $apiproduct;

    /**
     * CredentialProduct constructor.
     *
     * @param string $apiproduct
     * @param string $status
     */
    public function __construct(string $apiproduct, string $status)
    {
        $this->apiproduct = $apiproduct;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getApiproduct(): string
    {
        return $this->apiproduct;
    }

    /**
     * @param string $apiproduct
     */
    public function setApiproduct(string $apiproduct): void
    {
        $this->apiproduct = $apiproduct;
    }
}
