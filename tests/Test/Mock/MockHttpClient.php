<?php

namespace Apigee\Edge\Tests\Test\Mock;

use Http\Mock\Client;
use Psr\Http\Message\RequestInterface;

/**
 * Class MockHttpClient.
 *
 * Adds an additional getter to the Mock client until this PR is not going to be merged.
 *
 * @link https://github.com/php-http/mock-client/pull/23
 *
 * @package Apigee\Edge\Tests\Test\Mock
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class MockHttpClient extends Client
{
    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getLastRequest(): RequestInterface
    {
        return end($this->requests);
    }
}
