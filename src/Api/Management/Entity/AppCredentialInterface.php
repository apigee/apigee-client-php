<?php

namespace Apigee\Edge\Api\Management\Entity;

use Apigee\Edge\Entity\EntityInterface;
use Apigee\Edge\Entity\Property\AttributesPropertyInterface;
use Apigee\Edge\Entity\Property\ScopesPropertyInterface;
use Apigee\Edge\Entity\Property\StatusPropertyInterface;

/**
 * Interface AppCredentialInterface.
 *
 * @package Apigee\Edge\Api\Management\Entity
 * @author Dezső Biczó <mxr576@gmail.com>
 */
interface AppCredentialInterface extends
    EntityInterface,
    AttributesPropertyInterface,
    ScopesPropertyInterface,
    StatusPropertyInterface
{

}
