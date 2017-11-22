<?php

namespace Apigee\Edge\Exception;

use Http\Message\Formatter;
use Http\Message\Formatter\FullHttpMessageFormatter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class ApiException.
 *
 * General exception class for API communication errors.
 *
 * @package Apigee\Edge\Exception
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class ApiException extends \RuntimeException
{
    /** @var RequestInterface */
    protected $request;

    /** @var ResponseInterface */
    protected $response;

    /** @var FullHttpMessageFormatter */
    protected $formatter;

    /** @var string */
    protected $edgeErrorCode;

    /**
     * ApiException constructor.
     *
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @param Formatter|null $formatter
     * @param Throwable|null $previous
     */
    public function __construct(
        ResponseInterface $response,
        RequestInterface $request,
        Formatter $formatter = null,
        Throwable $previous = null
    ) {
        $this->formatter = $formatter ?: new FullHttpMessageFormatter();
        $this->request = $request;
        $this->response = $response;
        $message = $response->getReasonPhrase();
        // Try to parse Edge error message and error code from the response body.
        $contentTypeHeader = $response->getHeaderLine('Content-Type');
        if ($contentTypeHeader && strpos($contentTypeHeader, 'application/json') !== false) {
            $array = json_decode($response->getBody(), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (array_key_exists('code', $array)) {
                    $this->edgeErrorCode = $array['code'];
                }
                if (array_key_exists('message', $array)) {
                    $message = $array['message'];
                }
            }
        }
        parent::__construct($message, $response->getStatusCode(), $previous);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return Formatter
     */
    public function getFormatter(): Formatter
    {
        return $this->formatter;
    }

    /**
     * @return null|string
     */
    public function getEdgeErrorCode(): ?string
    {
        return $this->edgeErrorCode;
    }

    public function __toString()
    {
        return sprintf(
            "Request:\n%s\nResponse:\n%s\n",
            $this->formatter->formatRequest($this->request),
            $this->formatter->formatResponse($this->response)
        );
    }
}
