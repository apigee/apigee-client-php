<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Controller;

use Apigee\Edge\Entity\EntityDenormalizer;
use Apigee\Edge\Entity\EntityFactory;
use Apigee\Edge\Entity\EntityFactoryInterface;
use Apigee\Edge\Entity\EntityNormalizer;
use Apigee\Edge\HttpClient\ClientInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class AbstractEntityController.
 *
 * Base controller for communicating Apigee Edge endpoints that accepts and returns data that can be serialized and
 * deserialized as entities.
 */
abstract class AbstractEntityController extends AbstractController
{
    /**
     * @var EntityFactoryInterface Entity factory that can return an entity which can be used as an internal
     * representation of the Apigee Edge API response.
     */
    protected $entityFactory;

    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    protected $entitySerializer;

    /**
     * AbstractEntityController constructor.
     *
     * @param ClientInterface|null $client
     * @param EntityFactoryInterface|null $entityFactory
     */
    public function __construct(ClientInterface $client = null, EntityFactoryInterface $entityFactory = null)
    {
        parent::__construct($client);
        $this->entityFactory = $entityFactory ?: new EntityFactory();
        $this->entitySerializer = new Serializer(
            $this->entityNormalizers(),
            $this->entityEncoders()
        );
    }

    /**
     * Returns normalizers and denormalizers used by entity serializer for transforming entity data from/to objects.
     *
     * @return \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[]
     */
    protected function entityNormalizers()
    {
        return [new EntityNormalizer(), new EntityDenormalizer()];
    }

    /**
     * Returns encoders and decoders used by entity serializer for reading and writing serialized entity data.
     *
     * @return \Symfony\Component\Serializer\Encoder\EncoderInterface[]|\Symfony\Component\Serializer\Encoder\DecoderInterface[]
     */
    protected function entityEncoders()
    {
        // Keep the same structure that we get from Edge, do not transforms objects to arrays.
        return [new JsonEncoder(null, new JsonDecode())];
    }

    /**
     * Returns the entity type specific base url for an API call.
     *
     * @param string $entityId
     *
     * @return UriInterface
     */
    protected function getEntityEndpointUri(string $entityId): UriInterface
    {
        return $this->getBaseEndpointUri()->withPath(sprintf('%s/%s', $this->getBaseEndpointUri(), $entityId));
    }
}
