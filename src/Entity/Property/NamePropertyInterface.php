<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Interface NamePropertyInterface.
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface NamePropertyInterface
{
    /**
     * @return string
     */
    public function getName(): ?string;

    /**
     * @param string $name
     */
    public function setName(string $name): void;
}
