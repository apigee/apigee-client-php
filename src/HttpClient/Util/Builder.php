<?php

namespace Apigee\Edge\HttpClient\Util;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;

/**
 * Class Builder.
 *
 * @package Apigee\Edge\HttpClient\Util
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class Builder implements BuilderInterface
{
    use BuilderAwareTrait;

    /**
     * Builder constructor.
     * @param HttpClient|null $httpClient
     * @param RequestFactory|null $requestFactory
     * @param StreamFactory|null $streamFactory
     */
    public function __construct(
        HttpClient $httpClient = null,
        RequestFactory $requestFactory = null,
        StreamFactory $streamFactory = null
    )
    {
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
        $this->streamFactory = $streamFactory ?: StreamFactoryDiscovery::find();
    }
}
