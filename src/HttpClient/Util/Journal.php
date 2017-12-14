<?php

namespace Apigee\Edge\HttpClient\Util;

use Http\Client\Common\Plugin\HistoryPlugin;
use Http\Client\Common\Plugin\Journal as JournalInterface;
use Http\Client\Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Journal.
 *
 * Stores the last request, response and response exception by using the HistoryPlugin.
 *
 * @author Dezső Biczó <mxr576@gmail.com>
 *
 * @see HistoryPlugin
 */
class Journal implements JournalInterface
{
    /** @var RequestInterface */
    protected $lastRequest;

    /** @var ResponseInterface */
    protected $lastResponse;

    /** @var \Exception */
    protected $lastException;

    /**
     * Indicates whether the last request was successful or not.
     *
     * @var bool
     */
    protected $success = true;

    /**
     * Record a successful call.
     *
     * @param RequestInterface $request Request use to make the call
     * @param ResponseInterface $response Response returned by the call
     */
    public function addSuccess(RequestInterface $request, ResponseInterface $response)
    {
        $this->lastRequest = $request;
        $this->lastResponse = $response;
        $this->success = true;
    }

    /**
     * Record a failed call.
     *
     * @param RequestInterface $request Request use to make the call
     * @param Exception $exception Exception returned by the call
     */
    public function addFailure(RequestInterface $request, Exception $exception)
    {
        $this->lastRequest = $request;
        $this->lastException = $exception;
    }

    /**
     * @return RequestInterface
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * @return ResponseInterface
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * @return \Exception
     */
    public function getLastException()
    {
        return $this->lastException;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }
}
