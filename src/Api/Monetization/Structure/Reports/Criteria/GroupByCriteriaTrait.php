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

trait GroupByCriteriaTrait
{
    /** @var string[] */
    protected $groupBy = [];

    /**
     * @return string[]
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    /**
     * @param string ...$groupBy
     *
     * @return self
     *
     * @deprecated in 3.0.7, will be removed in 4.0.0. No longer needed.
     * https://github.com/apigee/apigee-client-php/issues/373
     */
    public function groupBy(string ...$groupBy): self
    {
        trigger_error(__METHOD__ . ' is deprecated in 3.0.7, will be removed in 4.0.0: use setGroupBy() instead.', E_USER_DEPRECATED);

        return $this->setGroupBy(...$groupBy);
    }

    /**
     * @param string ...$groupBy
     *
     * @return self
     */
    public function setGroupBy(string ...$groupBy): self
    {
        $this->groupBy = $groupBy;

        return $this;
    }
}
