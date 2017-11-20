<?php

namespace Apigee\Edge\Entity\Property;

use Apigee\Edge\Structure\AttributesProperty;

/**
 * Class AttributesAttributeInterface.
 *
 * @package Apigee\Edge\Entity\Attribute
 * @author Dezső Biczó <mxr576@gmail.com>
 * @see AttributesPropertyAwareTrait
 */
interface AttributesPropertyInterface
{
    public function getAttributes(): AttributesProperty;

    public function setAttributes(AttributesProperty $attributes): void;

    public function getAttributeValue(string $attribute): ?string;

    public function addAttribute(string $name, string $value): void;

    public function hasAttribute(string $name): bool;

    public function deleteAttribute(string $name): void;
}
