<?php

namespace Apigee\Edge\Api\Management\Controller;

use Apigee\Edge\Structure\AttributesProperty;
use Apigee\Edge\Structure\AttributesPropertyDenormalizer;
use Apigee\Edge\Structure\AttributesPropertyNormalizer;
use Psr\Http\Message\UriInterface;

/**
 * Trait AttributesAwareEntityControllerTrait.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
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
        $responseArray = $this->parseResponseToArray($this->client->get($this->getEntityAttributesUri($entityId)));

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
        $responseArray = $this->parseResponseToArray($this->client->get(
            $this->getEntityAttributeUri($entityId, $name)
        ));

        return $responseArray['value'];
    }

    /**
     * @inheritdoc
     */
    public function updateAttributes(string $entityId, AttributesProperty $attributes): AttributesProperty
    {
        $responseArray = $this->parseResponseToArray(
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
        $responseArray = $this->parseResponseToArray($this->client->post(
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
