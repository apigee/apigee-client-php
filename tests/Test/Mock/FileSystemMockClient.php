<?php

namespace Apigee\Edge\Tests\Test\Mock;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

/**
 * Class FileSystemMockClient.
 *
 * Loads the content of an HTTP response from the file system.
 *
 * @package Apigee\Edge\Tests\Test\Mock
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class FileSystemMockClient extends MockHttpClient
{
    /**
     * @inheritdoc
     */
    public function sendRequest(RequestInterface $request)
    {
        $response = parent::sendRequest($request);
        $default_response = $this->responseFactory->createResponse();
        // If parent has not responded with a default response then return with that.
        if ($response != $default_response) {
            return $response;
        }
        // Parse URI to a valid file system path.
        $filePath = rtrim($request->getUri()->getPath(), '/');
        $pattern = '/[\.+=@]/';
        $filePath = preg_replace($pattern, '-', $filePath);
        $filePath = "./tests/offline-test-data{$filePath}/";
        $fileName = $request->getMethod();
        if ($request->getUri()->getQuery()) {
            $fileName .= '-';
            $params = explode('&', $request->getUri()->getQuery());
            foreach ($params as $param) {
                list($key, $value) = explode('=', $param);
                $fileName .= "{$key}-{$value}_";
                $fileName = preg_replace($pattern, '-', rawurldecode($fileName));
            }
            $fileName = rtrim($fileName, '_');
        }
        $fileName .= '.json';
        $filePath .= $fileName;
        $file = file_get_contents($filePath);
        if (!$file) {
            throw new \Exception(sprintf('Unable to load file %s.', $filePath));
        }
        return new Response(200, ['Content-Type' => 'application/json'], $file);
    }
}
