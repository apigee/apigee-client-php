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

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\CommonEntityPropertiesAwareTrait;
use Apigee\Edge\Entity\Entity;
use Apigee\Edge\Entity\Property\DisplayNamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\EnvironmentsPropertyAwareTrait;
use Apigee\Edge\Entity\Property\NamePropertyAwareTrait;
use Apigee\Edge\Entity\Property\PropertiesPropertyAwareTrait;
use Apigee\Edge\Structure\PropertiesProperty;

/**
 * Describes an Organization entity.
 */
class Organization extends Entity implements OrganizationInterface
{
    use DisplayNamePropertyAwareTrait;
    use CommonEntityPropertiesAwareTrait;
    use EnvironmentsPropertyAwareTrait;
    use NamePropertyAwareTrait;
    use PropertiesPropertyAwareTrait;

    protected const TYPES = [
        'trial',
        'paid',
    ];

    /** @var string */
    protected $type;

    /**
     * Organization constructor.
     *
     * @param array $values
     *
     * @throws \ReflectionException
     */
    public function __construct(array $values = [])
    {
        $this->properties = new PropertiesProperty();
        parent::__construct($values);
    }

    /**
     * @inheritdoc
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType(string $type): void
    {
        if (!in_array($type, self::TYPES)) {
            throw new \InvalidArgumentException("{$type} type is not a valid.");
        }
        $this->type = $type;
    }

    /**
     * @inheritdoc
     */
    public function getTypes(): array
    {
        return self::TYPES;
    }
}
