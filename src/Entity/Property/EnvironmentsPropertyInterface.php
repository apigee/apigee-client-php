<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Interface EnvironmentsPropertyInterface.
 *
 * @package Apigee\Edge\Entity\Property
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
