<?php

namespace Apigee\Edge\Tests\Test\Mock;

use GuzzleHttp\Psr7\Response;
use Http\Client\Common\HttpAsyncClientEmulator;
use Http\Client\Exception\RequestException;
use Http\Client\HttpClient;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Psr\Http\Message\RequestInterface;

/**
 * Class FileSystemMockClient.
 *
 * Loads the content of an HTTP response from the file system.
 *
 * @package Apigee\Edge\Tests\Test\Mock
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class FileSystemMockClient implements MockClientInterface
{
    use HttpAsyncClientEmulator;

    protected $filesystem;

    /**
     * FileSystemMockClient constructor.
     *
     * @param \League\Flysystem\AdapterInterface|null $adapter
     */
    public function __construct(AdapterInterface $adapter = null)
    {
        if ($adapter === null) {
            $folder = getenv('APIGEE_PHP_SDK_OFFLINE_TEST_DATA_FOLDER') ?: './tests/offline-test-data';
            $adapter = new Local($folder);
        }
        $this->filesystem = new Filesystem($adapter);
    }

    /**
     * {@inheritdoc}
     *
     * @see HttpClient::sendRequest
     */
    public function sendRequest(RequestInterface $request)
    {
        $path = $this->transformRequestToPath($request);
        try {
            $content = $this->filesystem->read($path);
        } catch (FileNotFoundException $e) {
            throw new RequestException($e->getMessage(), $request, $e);
        }
        if (!$content) {
            throw new RequestException(sprintf('Unable to read content of file at %s path.', $path), $request);
        }
        return new Response(200, ['Content-Type' => 'application/json'], $content);
    }

    /**
     * Transforms a request to a valid file system path.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return string
     */
    protected function transformRequestToPath(RequestInterface $request) : string
    {
        $filePath = rtrim($request->getUri()->getPath(), '/');
        $fileName = $request->getMethod();
        if ($request->getUri()->getQuery()) {
            $fileName .= '_';
            $raw_query_params = [];
            parse_str($request->getUri()->getQuery(), $raw_query_params);
            ksort($raw_query_params);
            $query_params = http_build_query($raw_query_params, null, '-');
            $fileName .= $query_params;
        }
        $fileName .= '.json';
        $filePath .= '/' . $fileName;
        return rawurldecode($filePath);
    }
}
