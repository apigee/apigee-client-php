<?php

namespace Apigee\Edge\Entity;

use Apigee\Edge\Structure\CpsListLimitInterface;

/**
 * Interface EntityControllerInterface.
 *
 * Describes public behavior of entity controllers that works with entities that belongs to an organization on Edge.
 * (99% of entities belongs to an organization on Edge, except the organization entity itself.)
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface EntityControllerInterface extends BaseEntityControllerInterface
{
    /**
     * @return string The name of the organization.
     */
    public function getOrganisation(): string;

    /**
     * @param string $orgName The name of the organization that the entity belongs.
     *
     * @return string
     *   The organization name.
     */
    public function setOrganisation(string $orgName): string;

    /**
     * Returns list of entities from Edge. The returned number of entities can be limited.
     *
     * @param CpsListLimitInterface|null $cpsLimit
     *
     * @return array
     */
    public function getEntities(CpsListLimitInterface $cpsLimit = null): array;

    /**
     * Returns list of entity ids from Edge. The returned number of entities can be limited.
     *
     * @param CpsListLimitInterface|null $cpsLimit
     *
     * @return array
     */
    public function getEntityIds(CpsListLimitInterface $cpsLimit = null): array;

    /**
     * Returns a representation of a Core Persistence Services (CPS) limit.
     *
     * This limit can be used list API calls on Edge to limit the number of returned
     * results but CPS is not enabled on all organisations.
     *
     * @param string $startKey
     *    The primary key of the entity that the list should start.
     * @param int $limit
     *    Number of entities to return.
     *
     * @return CpsListLimitInterface
     *
     * @link https://docs.apigee.com/api-services/content/api-reference-getting-started#cps
     */
    public function createCpsLimit(string $startKey, int $limit): CpsListLimitInterface;
}
