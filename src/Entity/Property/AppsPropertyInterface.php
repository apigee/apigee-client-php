<?php

namespace Apigee\Edge\Entity\Property;

/**
 * Interface AppsPropertyInterface.
 *
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
