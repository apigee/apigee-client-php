<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Interface NamePropertyInterface.
 *
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
