<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Interface EnvironmentsPropertyInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface EnvironmentsPropertyInterface
{
    /**
     * @return string[]
     */
    public function getEnvironments(): array;

    /**
     * @param string[] $environments
     */
    public function setEnvironments(array $environments): void;
}
