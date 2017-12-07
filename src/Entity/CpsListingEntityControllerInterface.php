<?php

namespace Apigee\Edge\Entity;

use Apigee\Edge\Structure\CpsListLimitInterface;

/**
 * Interface CpsListingEntityControllerInterface.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface CpsListingEntityControllerInterface
{
    /**
     * Returns list of entities from Edge. The returned number of entities can be limited.
     *
     * @param \Apigee\Edge\Structure\CpsListLimitInterface|null $cpsLimit
     *
     * @return array
     */
    public function getEntities(CpsListLimitInterface $cpsLimit = null): array;

    /**
     * Returns list of entity ids from Edge. The returned number of entities can be limited.
     *
     * @param \Apigee\Edge\Structure\CpsListLimitInterface|null $cpsLimit
     *
     * @return \Apigee\Edge\Entity\EntityInterface[]
     */
    public function getEntityIds(CpsListLimitInterface $cpsLimit = null): array;
}
