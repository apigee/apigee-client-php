<?php

/*
 * Copyright 2018 Google Inc.
 * Use of this source code is governed by a MIT-style license that can be found in the LICENSE file or
 * at https://opensource.org/licenses/MIT.
 */

namespace Apigee\Edge\Exception;

use Http\Message\Formatter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class InvalidJsonException.
 */
class InvalidJsonException extends ApiResponseException
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
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $jsonErrorMessage,
        ResponseInterface $response,
        RequestInterface $request,
        Formatter $formatter = null,
        \Throwable $previous = null
    ) {
        $this->jsonErrorMessage = $jsonErrorMessage;
        parent::__construct($response, $request, $jsonErrorMessage, 0, $previous, $formatter);
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return sprintf("%s\n%s", $this->jsonErrorMessage, parent::__toString());
    }

    /**
     * @return string
     */
    public function getJsonErrorMessage()
    {
        return $this->jsonErrorMessage;
    }
}
