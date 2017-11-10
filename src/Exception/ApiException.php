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

    /**
     * ApiException constructor.
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
    )
    {
        $this->formatter = $formatter ?: new FullHttpMessageFormatter();
        $this->request = $request;
        $this->response = $response;
        parent::__construct($response->getReasonPhrase(), $response->getStatusCode(), $previous);
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

    public function __toString()
    {
        return sprintf(
            "Request:\n%s\nResponse:\n%s\n",
            $this->formatter->formatRequest($this->request),
            $this->formatter->formatResponse($this->response)
        );
    }

}
