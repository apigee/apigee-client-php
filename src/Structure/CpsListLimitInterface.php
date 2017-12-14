<?php

namespace Apigee\Edge\Structure;

/**
 * Class CpsListLimitInterface.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface CpsListLimitInterface
{
    /**
     * @return string The primary key of the entity that the list will start.
     */
    public function getStartKey(): string;

    /**
     * @param string $startKey
     *    The primary key of the entity that the list will start.
     *
     * @return string
     */
    public function setStartKey(string $startKey): string;

    /**
     * @return int Number of entities to return.
     */
    public function getLimit(): int;

    /**
     * @param int $limit
     *   Number of entities to return.
     *
     * @return int
     */
    public function setLimit(int $limit): int;
}
