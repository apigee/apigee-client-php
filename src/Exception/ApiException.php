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
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class ApiException extends \RuntimeException
{
    /** @var \Psr\Http\Message\RequestInterface */
    protected $request;

    /** @var \Psr\Http\Message\ResponseInterface */
    protected $response;

    /** @var \Http\Message\Formatter */
    protected $formatter;

    /** @var string */
    protected $edgeErrorCode;

    /**
     * ApiException constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Psr\Http\Message\RequestInterface $request
     * @param \Http\Message\Formatter|null $formatter
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
        if ($contentTypeHeader && false !== strpos($contentTypeHeader, 'application/json')) {
            $array = json_decode((string) $response->getBody(), true);
            if (JSON_ERROR_NONE === json_last_error()) {
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
     * @inheritdoc
     */
    public function __toString()
    {
        return sprintf(
            "Request:\n%s\nResponse:\n%s\n",
            $this->formatter->formatRequest($this->request),
            $this->formatter->formatResponse($this->response)
        );
    }

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return \Http\Message\Formatter
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
}
