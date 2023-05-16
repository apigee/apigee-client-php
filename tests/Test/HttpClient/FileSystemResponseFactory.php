<?php

/*
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Apigee\Edge\Tests\Test\HttpClient;

use GuzzleHttp\Psr7\Response;
use Http\Client\Exception\RequestException;
use Http\Message\ResponseFactory;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;

/**
 * Class FileSystemResponseFactory.
 */
class FileSystemResponseFactory implements ResponseFactory
{
    /** @var \League\Flysystem\Filesystem */
    private $filesystem;

    /** @var \Symfony\Component\Serializer\Encoder\DecoderInterface */
    private $decoder;

    /**
     * FileSystemResponseFactory constructor.
     *
     * @param \League\Flysystem\AdapterInterface|null $adapter
     *   FlySystem adapter.
     * @param DecoderInterface|null $decoder
     *   Decoder interface for reading request bodies. Default is JsonDecode.
     */
    public function __construct(AdapterInterface $adapter = null, DecoderInterface $decoder = null)
    {
        if (null === $adapter) {
            $defaultFolder = realpath(sprintf(
                '%s%s..%s..%soffline-test-data',
                dirname(__FILE__),
                DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR
            ));
            $folder = getenv('APIGEE_PHP_SDK_OFFLINE_TEST_DATA_FOLDER') ?: $defaultFolder;
            $adapter = new Local($folder);
        }
        $this->filesystem = new Filesystem($adapter);
        $this->decoder = $decoder ?: new JsonDecode([true]);
    }

    /**
     * {@inheritdoc}
     */
    public function createResponse(
        $statusCode = 200,
        $reasonPhrase = null,
        array $headers = [],
        $body = null,
        $protocolVersion = '1.1'
    ) {
        return new Response(
            $statusCode,
            $headers,
            $body,
            $protocolVersion,
            $reasonPhrase
        );
    }

    /**
     * Creates a response for a request by using saved JSON samples.
     *
     * @param RequestInterface $request
     * @param int $statusCode
     * @param null $reasonPhrase
     * @param array $headers
     * @param string $protocolVersion
     *
     * @return Response|\Psr\Http\Message\ResponseInterface
     */
    public function createResponseForRequest(
        RequestInterface $request,
        $statusCode = 200,
        $reasonPhrase = null,
        array $headers = [],
        $protocolVersion = '1.1'
    ) {
        $path = $this->transformRequestToPath($request);
        try {
            $output = $this->filesystem->read($path);
        } catch (FileNotFoundException $e) {
            throw new RequestException($e->getMessage(), $request, $e);
        }
        if (!$output) {
            throw new RequestException(sprintf('Unable to read content of file at %s path.', $path), $request);
        }

        return $this->createResponse($statusCode, $reasonPhrase, $headers, $output, $protocolVersion);
    }

    /**
     * Transforms a request to a valid file system path.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return string
     */
    protected function transformRequestToPath(RequestInterface $request): string
    {
        $filePath = rtrim($request->getUri()->getPath(), DIRECTORY_SEPARATOR);
        $fileName = $request->getMethod();
        if ($request->getUri()->getQuery()) {
            $fileName .= '_';
            $raw_query_params = [];
            parse_str($request->getUri()->getQuery(), $raw_query_params);
            $raw_query_params = preg_replace('/[\W]/', '', $raw_query_params);
            ksort($raw_query_params);
            $query_params = http_build_query($raw_query_params, null, '-');
            $fileName .= $query_params;
        }
        $fileName .= '.json';
        $filePath .= DIRECTORY_SEPARATOR . $fileName;

        return rawurldecode(($filePath));
    }
}
