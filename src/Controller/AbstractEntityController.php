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

namespace Apigee\Edge\Controller;

use Apigee\Edge\ClientInterface;
use Apigee\Edge\Entity\EntityTransformer;
use Apigee\Edge\Entity\EntityTransformerInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Class AbstractEntityController.
 *
 * Base controller for communicating Apigee Edge endpoints that accepts and returns data that can be serialized and
 * deserialized as entities.
 */
abstract class AbstractEntityController extends AbstractController
{
    use EntityTransformerAwareTrait;
    use EntityEndpointAwareControllerTrait;
    use EntityClassAwareTrait;

    /**
     * @var \Apigee\Edge\Entity\EntityTransformerInterface
     */
    protected $entityTransformer;

    /**
     * AbstractEntityController constructor.
     *
     * @param \Apigee\Edge\ClientInterface $client
     *   Apigee Edge API client.
     * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[] $entityNormalizers
     *   Array of entity normalizers and denormalizers that are being called earlier than the default ones.
     */
    public function __construct(ClientInterface $client, array $entityNormalizers = [])
    {
        parent::__construct($client);
        $this->entityTransformer = $this->buildEntityTransformer($entityNormalizers);
    }

    /**
     * @inheritdoc
     */
    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath("{$this->getBaseEndpointUri()}/{$entityId}");
    }

    /**
     * Returns a configured entity transformer.
     *
     * @param array $normalizers
     * @param array $encoders
     * @param \Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface|null $classMetadataFactory
     * @param \Symfony\Component\Serializer\NameConverter\NameConverterInterface|null $nameConverter
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface|null $propertyAccessor
     * @param \Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface|null $propertyTypeExtractor
     *
     * @return \Apigee\Edge\Entity\EntityTransformerInterface
     */
    protected function buildEntityTransformer(array $normalizers = [], array $encoders = [], ClassMetadataFactoryInterface $classMetadataFactory = null, NameConverterInterface $nameConverter = null, PropertyAccessorInterface $propertyAccessor = null, PropertyTypeExtractorInterface $propertyTypeExtractor = null): EntityTransformerInterface
    {
        return new EntityTransformer($normalizers, $encoders, $classMetadataFactory, $nameConverter, $propertyAccessor, $propertyTypeExtractor);
    }

    /**
     * @inheritDoc
     */
    protected function getEntityTransformer(): EntityTransformerInterface
    {
        return $this->entityTransformer;
    }
}
