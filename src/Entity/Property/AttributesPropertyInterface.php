<?php

namespace Apigee\Edge\Entity\Property;

use Apigee\Edge\Structure\AttributesProperty;

/**
 * Class AttributesAttributeInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @see AttributesPropertyAwareTrait
 */
interface AttributesPropertyInterface
{
    /**
     * @return \Apigee\Edge\Structure\AttributesProperty
     */
    public function getAttributes(): AttributesProperty;

    /**
     * @param \Apigee\Edge\Structure\AttributesProperty $attributes
     */
    public function setAttributes(AttributesProperty $attributes): void;

    /**
     * @param string $attribute
     *
     * @return null|string
     */
    public function getAttributeValue(string $attribute): ?string;

    /**
     * @param string $name
     * @param string $value
     */
    public function setAttribute(string $name, string $value): void;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasAttribute(string $name): bool;

    /**
     * @param string $name
     */
    public function deleteAttribute(string $name): void;
}
