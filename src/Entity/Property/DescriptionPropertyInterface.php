<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Interface DescriptionPropertyInterface.
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface DescriptionPropertyInterface
{
    /**
     * @return string
     */
    public function getDescription(): ?string;

    /**
     * @param string $description
     */
    public function setDescription(string $description): void;
}
