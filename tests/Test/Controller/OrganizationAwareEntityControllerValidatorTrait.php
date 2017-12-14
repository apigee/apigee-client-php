<?php

namespace Apigee\Edge\Tests\Test\Controller;

/**
 * Trait OrganizationAwareEntityControllerValidatorTrait.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
trait OrganizationAwareEntityControllerValidatorTrait
{
    protected static function getOrganization()
    {
        return getenv('APIGEE_PHP_SDK_ORGANIZATION') ?: 'phpunit';
    }
}
