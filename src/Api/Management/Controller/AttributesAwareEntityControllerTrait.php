<?php

/**
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

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Structure\AttributesPropertyDenormalizer;
use Apigee\Edge\Structure\AttributesPropertyNormalizer;
use Psr\Http\Message\UriInterface;

/**
 * Trait AttributesAwareEntityControllerTrait.
 *
 *
 * @see AttributesAwareEntityControllerInterface
 */
trait AttributesAwareEntityControllerTrait
{
    /**
     * @inheritdoc
     */
    public function getAttributes(string $entityId): AttributesProperty
    {
        $responseArray = $this->responseToArray($this->client->get($this->getEntityAttributesUri($entityId)));

        return $this->getAttributesPropertyDenormalizer()->denormalize(
            $responseArray['attribute'],
            AttributesProperty::class
        );
    }

    /**
     * @inheritdoc
     */
    public function getAttribute(string $entityId, string $name): string
    {
        $responseArray = $this->responseToArray($this->client->get(
            $this->getEntityAttributeUri($entityId, $name)
        ));

        return $responseArray['value'];
    }

    /**
     * @inheritdoc
     */
    public function updateAttributes(string $entityId, AttributesProperty $attributes): AttributesProperty
    {
        $responseArray = $this->responseToArray(
            $this->client->post(
                $this->getEntityAttributesUri($entityId),
                (string) json_encode((object) [
                    'attribute' => $this->getAttributesPropertyNormalizer()->normalize($attributes),
                ])
            )
        );

        return $this->getAttributesPropertyDenormalizer()->denormalize(
            $responseArray['attribute'],
            AttributesProperty::class
        );
    }

    /**
     * @inheritdoc
     */
    public function updateAttribute(string $entityId, string $name, string $value): string
    {
        $value = (string) json_encode((object) ['value' => $value]);
        $responseArray = $this->responseToArray($this->client->post(
            $this->getEntityAttributeUri($entityId, $name),
            $value
        ));

        return $responseArray['value'];
    }

    /**
     * @inheritdoc
     */
    public function deleteAttribute(string $entityId, string $name): void
    {
        $this->client->delete($this->getEntityAttributeUri($entityId, $name));
    }

    /**
     * @return \Apigee\Edge\Structure\AttributesPropertyNormalizer
     */
    protected function getAttributesPropertyNormalizer(): AttributesPropertyNormalizer
    {
        static $normalizer;
        if (!$normalizer) {
            $normalizer = new AttributesPropertyNormalizer();
        }

        return $normalizer;
    }

    /**
     * @return \Apigee\Edge\Structure\AttributesPropertyDenormalizer
     */
    protected function getAttributesPropertyDenormalizer(): AttributesPropertyDenormalizer
    {
        static $denormalizer;
        if (!$denormalizer) {
            $denormalizer = new AttributesPropertyDenormalizer();
        }

        return $denormalizer;
    }

    /**
     * Returns the base URI of an entity type's attributes.
     *
     * @param string $entityId
     *
     * @return UriInterface
     */
    protected function getEntityAttributesUri(string $entityId): UriInterface
    {
        $uri = $this->getEntityEndpointUri($entityId)->withPath(
            $this->getEntityEndpointUri($entityId) . '/attributes'
        );

        return $uri;
    }

    /**
     * Returns an URI to an entity's attribute.
     *
     * @param string $entityId
     * @param string $name
     *
     * @return UriInterface
     */
    protected function getEntityAttributeUri(string $entityId, string $name): UriInterface
    {
        $uri = $this->getEntityAttributesUri($entityId)->withPath(
            $this->getEntityAttributesUri($entityId) . '/' . $name
        );

        return $uri;
    }
}
