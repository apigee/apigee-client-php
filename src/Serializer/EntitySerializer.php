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

namespace Apigee\Edge\Serializer;

use Apigee\Edge\Denormalizer\EdgeDateDenormalizer;
use Apigee\Edge\Denormalizer\KeyValueMapDenormalizer;
use Apigee\Edge\Denormalizer\ObjectDenormalizer;
use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Normalizer\EdgeDateNormalizer;
use Apigee\Edge\Normalizer\KeyValueMapNormalizer;
use Apigee\Edge\Normalizer\ObjectNormalizer;
use Psr\Http\Message\ResponseInterface;
use ReflectionMethod;
use ReflectionObject;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Serializer;
use TypeError;

/**
 * Serializes, normalizes and denormalizes entities.
 */
class EntitySerializer implements EntitySerializerInterface
{
    /** @var Serializer */
    private $serializer;

    /**
     * The API client only communicates in JSON with Apigee Edge.
     *
     * @var string
     */
    private $format = JsonEncoder::FORMAT;

    /**
     * EntitySerializer constructor.
     *
     * @param \Symfony\Component\Serializer\Normalizer\NormalizerInterface[]|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface[] $normalizers
     */
    public function __construct(array $normalizers = [])
    {
        $normalizers = array_merge($normalizers, [
            // KVM is a commonly used object type. Let's make its normalizers/denormalizers available by default
            new KeyValueMapNormalizer(),
            new KeyValueMapDenormalizer(),
            // Transforms Unix epoch timestamps to date objects and vice-versa.
            new EdgeDateNormalizer(),
            new EdgeDateDenormalizer(),
            // Takes care of denormalizations of array objects.
            new ArrayDenormalizer(),
            new ObjectNormalizer(),
            new ObjectDenormalizer(),
        ]
        );
        $this->serializer = new Serializer($normalizers, [$this->jsonEncoder()]);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        return $this->serializer->denormalize($data, $type, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $this->format === $format && $this->serializer->supportsDenormalization($data, $type, $format);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $this->serializer->normalize($object, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $this->format === $format && $this->serializer->supportsNormalization($data, $format);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, $format, array $context = []): string
    {
        if (!$this->supportsEncoding($format)) {
            throw new NotEncodableValueException(sprintf('Serialization for the format %s is not supported. Only %s supported.', $format, $this->format));
        }

        return $this->serializer->serialize($data, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($data, $type, $format, array $context = []): mixed
    {
        if (!$this->supportsDecoding($format)) {
            throw new NotEncodableValueException(sprintf('Deserialization for the format %s is not supported. Only %s is supported.', $format, $this->format));
        }

        $context['json_decode_associative'] = false;

        return $this->serializer->deserialize($data, $type, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function setPropertiesFromResponse(ResponseInterface $response, EntityInterface $entity): void
    {
        // Parse Edge response to a temporary entity (with the same type as $entity).
        // This is a crucial step because Edge response must be transformed before we would be able use it with some
        // of our setters (ex.: attributes).
        /** @var object $tmp */
        $tmp = $this->deserialize(
            (string) $response->getBody(),
            get_class($entity),
            $this->format
        );
        $ro = new ReflectionObject($entity);
        // Copy property values from the temporary entity to $entity.
        foreach ($ro->getProperties() as $property) {
            // Ensure that these methods are exist. This is always true for all SDK entities but we can not be sure
            // about custom implementation.
            $setter = 'set' . ucfirst($property->getName());
            if (!$ro->hasMethod($setter)) {
                continue;
            }

            $getter = 'get' . ucfirst($property->getName());
            if (!$ro->hasMethod($getter)) {
                $getter = 'is' . ucfirst($property->getName());
                if (!$ro->hasMethod($getter)) {
                    continue;
                }
            }

            $rm = new ReflectionMethod($entity, $setter);
            $value = $tmp->{$getter}();
            // Exclude null values.
            // (An entity property value is null (internally) if it is scalar
            // and API response that from the entity object had been
            // created did not contain the property or a non-empty value for
            // the property.)
            if (null !== $value) {
                try {
                    $rm->invoke($entity, $value);
                } catch (TypeError $error) {
                    // Auto-retry, pass the value as variable-length arguments.
                    if (is_array($value)) {
                        // Clear the value of the property.
                        if (empty($value)) {
                            $rm->invoke($entity);
                        } else {
                            $rm->invoke($entity, ...$value);
                        }
                    } else {
                        throw $error;
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decode($data, $format, array $context = [])
    {
        return $this->serializer->decode($data, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDecoding($format)
    {
        return $this->format === $format && $this->serializer->supportsDecoding($format);
    }

    /**
     * {@inheritdoc}
     */
    public function encode($data, $format, array $context = []): string
    {
        return $this->serializer->encode($data, $format, $context = []);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding($format): bool
    {
        return $this->format === $format && $this->serializer->supportsEncoding($format);
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            EntitySerializerInterface::class => true,
        ];
    }

    /**
     * Allows subclasses to replace the default JSON encoder.
     *
     * @return JsonEncoder
     */
    protected function jsonEncoder(): JsonEncoder
    {
        // Keep the same structure that we get from Apigee Edge, do not
        // transforms objects to arrays.
        return new JsonEncoder(new JsonDecode());
    }
}
