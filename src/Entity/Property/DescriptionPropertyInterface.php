<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Interface DescriptionPropertyInterface.
 *
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
