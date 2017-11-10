<?php

namespace Apigee\Edge\Entity;

/**
 * Interface StatusAwareEntityControllerInterface.
 *
 * @package Apigee\Edge\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface StatusAwareEntityControllerInterface extends EntityControllerInterface
{
    public function setStatus(string $entityId, string $status): void;
}
