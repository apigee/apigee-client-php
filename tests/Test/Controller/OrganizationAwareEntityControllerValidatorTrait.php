<?php

namespace Apigee\Edge\Tests\Test\Controller;

/**
 * Trait OrganizationAwareEntityControllerValidatorTrait.
 *
 * @package Apigee\Edge\Tests\Test\Controller
 * @author Dezső Biczó <mxr576@gmail.com>
 */
trait OrganizationAwareEntityControllerValidatorTrait
{
    protected static function getOrganization()
    {
        return getenv('APIGEE_PHP_SDK_ORGANIZATION') ?: 'phpunit';
    }
}
