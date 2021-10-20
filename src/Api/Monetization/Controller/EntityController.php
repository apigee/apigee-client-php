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

namespace Apigee\Edge\Api\Monetization\Controller;

use Apigee\Edge\Api\Monetization\Serializer\EntitySerializer;
use Apigee\Edge\ClientInterface;
use Apigee\Edge\Controller\EntityController as BaseEntityController;
use Apigee\Edge\Serializer\EntitySerializerInterface;

/**
 * Monetization specific base entity controller.
 */
abstract class EntityController extends BaseEntityController
{
    /**
     * {@inheritdoc}
     */
    public function __construct(string $organization, ClientInterface $client, ?EntitySerializerInterface $entitySerializer = null)
    {
        $entitySerializer = $entitySerializer ?? new EntitySerializer();
        parent::__construct($organization, $client, $entitySerializer);
    }
}
