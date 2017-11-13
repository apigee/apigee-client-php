<?php

namespace Apigee\Edge\Exception;

use Http\Message\Formatter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class InvalidJsonException.
 *
 * @package Apigee\Edge\Exception
 * @author Dezső Biczó <mxr576@gmail.com>
 */
class InvalidJsonException extends ApiException
{
    /** @var string */
    protected $jsonErrorMessage;

    /**
     * InvalidJsonException constructor.
     *
     * @param string $jsonErrorMessage
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @param Formatter|null $formatter
     * @param Throwable|null $previous
     */
    public function __construct(
        string $jsonErrorMessage,
        ResponseInterface $response,
        RequestInterface $request,
        Formatter $formatter = null,
        Throwable $previous = null
    ) {
        $this->jsonErrorMessage = $jsonErrorMessage;
        parent::__construct($response, $request, $formatter, $previous);
    }

    /**
     * @return string
     */
    public function getJsonErrorMessage()
    {
        return $this->jsonErrorMessage;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return sprintf("%s\n%s", $this->jsonErrorMessage, parent::__toString());
    }
}
