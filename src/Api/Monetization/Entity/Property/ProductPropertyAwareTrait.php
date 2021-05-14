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

namespace Apigee\Edge\Api\Monetization\Entity\Property;

use Apigee\Edge\Api\Monetization\Entity\ApiProduct;

/**
 * Trait ProductPropertyAwareTrait.
 *
 * @see \Apigee\Edge\Api\Monetization\Entity\Property\ProductPropertyInterface
 */
trait ProductPropertyAwareTrait
{
    /** @var \Apigee\Edge\Api\Monetization\Entity\ApiProduct|null */
    protected $product = null;

    /**
     * {@inheritdoc}
     */
    public function getProduct(): ?ApiProduct
    {
        return $this->product;
    }

    /**
     * Sets the product information.
     *
     * @param \Apigee\Edge\Api\Monetization\Entity\ApiProduct $product
     *
     * @internal
     */
    public function setProduct(ApiProduct $product): void
    {
        $this->product = $product;
    }
}
