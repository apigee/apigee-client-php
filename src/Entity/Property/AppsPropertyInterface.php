<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Interface AppsPropertyInterface.
 *
 * @package Apigee\Edge\Entity\Property
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface AppsPropertyInterface
{
    /**
     * @return array App names.
     */
    public function getApps(): array;

    /**
     * @param string $appName App name.
     *
     * @return bool
     */
    public function hasApp(string $appName): bool;
}
