<?php

/*
 * Copyright 2023 Google LLC
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

namespace Apigee\Edge\Api\ApigeeX\Entity;

use Apigee\Edge\Entity\CommonEntityPropertiesAwareTrait;
use Apigee\Edge\Entity\Entity;
use Apigee\Edge\Entity\Property\AttributesPropertyAwareTrait;
use Apigee\Edge\Entity\Property\DisplayNamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\StatusPropertyAwareTrait;
use Apigee\Edge\Structure\AttributesProperty;

/**
 * Describes an AppGroup entity.
 */
class AppGroup extends Entity implements AppGroupInterface
{
    use DisplayNamePropertyAwareTrait;
    use NamePropertyAwareTrait;
    use AttributesPropertyAwareTrait;
    use CommonEntityPropertiesAwareTrait;
    use StatusPropertyAwareTrait;

    /** @var string|null */
    protected $correlationId;

    /** @var string|null */
    protected $channelId;

    /**
     * AppGroup constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->attributes = new AttributesProperty();
        parent::__construct($values);
    }

    /**
     * {@inheritdoc}
     */
    public function setCorrelationId(string $correlationId): void
    {
        $this->correlationId = $correlationId;
    }

    /**
     * {@inheritdoc}
     */
    public function getCorrelationId(): ?string
    {
        return $this->correlationId;
    }

    /**
     * {@inheritdoc}
     */
    public function setChannelId(string $channelId): void
    {
        $this->channelId = $channelId;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannelId(): ?string
    {
        return $this->channelId;
    }
}
