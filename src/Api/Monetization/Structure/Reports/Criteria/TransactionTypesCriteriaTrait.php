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

namespace Apigee\Edge\Api\Monetization\Structure\Reports\Criteria;

trait TransactionTypesCriteriaTrait
{
    /**
     * @var string[]
     */
    protected $transactionTypes = [];

    /**
     * @return string[]
     */
    public function getTransactionTypes(): array
    {
        return $this->transactionTypes;
    }

    /**
     * @param string ...$transactionTypes
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function transactionTypes(string ...$transactionTypes): self
    {
        trigger_error(__METHOD__ . ' is deprecated in 3.0.7, will be removed in 4.0.0: use setTransactionTypes() instead.', E_USER_DEPRECATED);

        return $this->setTransactionTypes(...$transactionTypes);
    }

    /**
     * @param string ...$transactionTypes
     *
     * @return self
     */
    public function setTransactionTypes(string ...$transactionTypes): self
    {
        $this->transactionTypes = $transactionTypes;

        return $this;
    }
}
