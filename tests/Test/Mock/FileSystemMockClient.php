<?php

namespace Apigee\Edge\Tests\Test\Mock;

use Http\Client\Common\HttpAsyncClientEmulator;
use Http\Client\HttpClient;
use League\Flysystem\AdapterInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Class FileSystemMockClient.
 *
 * Loads the content of an HTTP response from the file system.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class FileSystemMockClient implements MockClientInterface
{
    use HttpAsyncClientEmulator;

    /** @var FileSystemResponseFactory */
    private $fileSystemResponseFactory;

    /**
     * FileSystemMockClient constructor.
     *
     * @param \League\Flysystem\AdapterInterface|null $adapter
     */
    public function __construct(AdapterInterface $adapter = null)
    {
        $this->fileSystemResponseFactory = new FileSystemResponseFactory($adapter);
    }

    /**
     * {@inheritdoc}
     *
     * @see HttpClient::sendRequest
     */
    public function sendRequest(RequestInterface $request)
    {
        return $this->fileSystemResponseFactory->createResponseForRequest($request, 200, null, ['Content-Type' => 'application/json']);
    }
}
